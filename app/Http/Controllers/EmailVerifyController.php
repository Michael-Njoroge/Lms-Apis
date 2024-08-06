<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Validation\ValidationException;

class EmailVerifyController extends Controller
{
    public function verify(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            throw ValidationException::withMessages([
                'id' => ['The ID does not match any user.']
            ]);
        }

        if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            throw ValidationException::withMessages([
                'id' => ['The ID does not match.']
            ]);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw ValidationException::withMessages([
                'hash' => ['The hash does not match.']
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            $user->status = 'active';
            $user->save();
        }

        return response()->json(['message' => 'Email verified successfully'], 200);
    }
    public function resend(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Check your mail, Verification link sent.'], 200);
    }
}
