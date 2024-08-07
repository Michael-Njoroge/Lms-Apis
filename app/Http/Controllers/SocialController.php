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

            $user = User::updateOrCreate(
                [
                    'email' => $socialUser->getEmail(),
                ],
                [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'google_id' => $provider === 'google' ? $socialUser->getId() : null,
                    'github_id' => $provider === 'github' ? $socialUser->getId() : null,
                    'user_image' => $socialUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                ]
            );

            $token = $user->createToken('access_token')->accessToken;

            $createdUser = User::findOrFail($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $createdUser->id,
                    'firstname' => $createdUser->firstname,
                    'lastname' => $createdUser->lastname,
                    'token' => $token,
                    'email' => $createdUser->email,
                    'mobile' => $createdUser->mobile,
                    'role' => $createdUser->role,
                    'username' => strtolower($createdUser->firstname . $createdUser->lastname),
                    'profile' => $createdUser->user_image,
                ],
                'message' => 'Authentication successful!',
            ]);

        } catch (Exception $e) {
                        Log::error("Error in handleProviderCallback ($provider): " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
