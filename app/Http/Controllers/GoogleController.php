<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Resources\UsersResource;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ]);
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $code = $request->input('code');
            if (!$code) {
                return $this->sendError('No authorization code provided');
            }

            $googleUser = Socialite::driver('google')->stateless()->user();

            $nameParts = explode(' ', $googleUser->getName(), 2);
            $firstname = $nameParts[0];
            $lastname = isset($nameParts[1]) ? $nameParts[1] : '';

            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->getEmail(),
                ],
                [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'google_id' => $googleUser->getId(),
                    'user_image' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                ]
            );

            $user->createToken('Personal Access Token')->accessToken;

            return $this->sendResponse(UsersResource::make($user)
                    ->response()
                    ->getData(true),'Authentication successful. User information retrieved and token generated.');

        } catch (Exception $e) {
            Log::error('Error in handleGoogleCallback: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
