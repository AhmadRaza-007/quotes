<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function handleGoogleAuth(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'access_token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);

            return $this->handleSocialUser($googleUser, 'google');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Google authentication failed: ' . $e->getMessage()
            ], 401);
        }
    }

    public function handleFacebookAuth(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'access_token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $facebookUser = Socialite::driver('facebook')->stateless()->userFromToken($request->access_token);

            return $this->handleSocialUser($facebookUser, 'facebook');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Facebook authentication failed: ' . $e->getMessage()
            ], 401);
        }
    }

    private function handleSocialUser($socialUser, $provider)
    {
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(uniqid()), // Random password
                'avatar' => $socialUser->getAvatar(),
                'user_type' => 2,
                'provider' => $provider,
                'provider_id' => $socialUser->getId()
            ]);
        }

        // Generate token
        $token = $user->createToken("{$provider}_auth")->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ], 200);
    }
}
