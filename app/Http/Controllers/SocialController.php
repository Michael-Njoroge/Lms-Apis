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

class SocialController extends Controller
{
    // Redirect Method
    public function redirectToProvider($provider)
    {
        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()
        ]);
    }

    // Handle Callback
    public function handleProviderCallback(Request $request, $provider)
    {
        try {
            $code = $request->input('code');
            if (!$code) {
                return response()->json(['error' => 'No authorization code provided'], 400);
            }

            $socialUser = Socialite::driver($provider)->stateless()->user();

            $nameParts = explode(' ', $socialUser->getName(), 2);
            $firstname = $nameParts[0];
            $lastname = isset($nameParts[1]) ? $nameParts[1] : '';

            // Check if the user already exists
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'google_id' => $provider === 'google' ? $socialUser->getId() : $user->google_id,
                    'github_id' => $provider === 'github' ? $socialUser->getId() : $user->github_id,
                    'linkedin_id' => $provider === 'linkedin' ? $socialUser->getId() : $user->linkedin_id,
                    'user_image' => $socialUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'email' => $socialUser->getEmail(),
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'google_id' => $provider === 'google' ? $socialUser->getId() : null,
                    'github_id' => $provider === 'github' ? $socialUser->getId() : null,
                    'linkedin_id' => $provider === 'linkedin' ? $socialUser->getId() : null,
                    'user_image' => $socialUser->getAvatar(),
                    'password' => Hash::make(Str::random(24))
                ]);
            }

            $token = $user->createToken('access_token')->accessToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'token' => $token,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'role' => $user->role,
                    'username' => strtolower($user->firstname . $user->lastname),
                    'profile' => $user->user_image,
                ],
                'message' => 'Authentication successful!',
            ]);

        } catch (Exception $e) {
            Log::error("Error in handleProviderCallback ($provider): " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

}
