<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteFavourite;
use App\Models\QuoteLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{

    public function register(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // $token = $user->createToken($request->email)->plainTextToekn;
            $token = $user->createToken('signup')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage() . " on line number: " . $exception->getLine(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                'user_type' => 2
            ];

            if (Auth::attempt($credentials)) {

                $user = User::where('email', $request->email)->first();
                $token = $user->createToken($request->email)->plainTextToken;

                return response()->json([
                    'message' => 'success',
                    'token' => $token,
                    'user' => $user,
                ], 200);
            } else {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ], 401);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email);
            if ($user) {
                $resetCode = rand(100000, 999999);
                $user->update(['reset_code' => $resetCode]);
                // Mail::to($request->email)->send(new ResetCodeMail($resetCode));

                return response()->json([
                    'message' => 'success',
                    'reset_code' => $resetCode,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'not found',
                    'user' => 'user not found',
                ], 404);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'reset_code' => 'required',
                'password' => 'required|min:6',
            ]);

            $user = User::where('reset_code', $request->reset_code)->first();

            if ($user) {
                User::where('reset_code', $request->reset_code)->update([
                    'password' => Hash::make($request->password),
                    'reset_code' => null, // Update reset_code to null
                ]);
                $user->tokens()->delete();
                $token = $user->createToken('password update')->plainTextToken;

                return response()->json([
                    'message' => 'success',
                    'token' => $token,
                    'user' => $user,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'user not found',
                ], 401);
            }
        } catch (\Exception $th) {
            return response()->json([
                'message' => 'error',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            $inputs = $request->validate([
                'password' => 'required|min:6',
            ]);

            if ($inputs) {

                $user = auth()->user();
                $user_detail = User::whereId($user->id)->first();

                if ($user_detail && Hash::check($request->password, $user->password)) {

                    quoteFavourite::whereUserId($user->id)->delete();
                    quoteLike::whereUserId($user->id)->delete();
                    User::whereId($user->id)->delete();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'User Deleted Successfully',
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'please enter your correct Password',
                    ]);
                }
            } else {
                return  response()->json([
                    'status' => 'error',
                    'message' => "invalid param's Password",
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage() . " on line number: " . $exception->getLine(),
            ], 500);
        }
    }

    public function handleProviderCallback()
    {
        try {
            // Retrieve user information from Google
            $user = Socialite::driver('google')->stateless()->userFromToken('ya29.a0AXooCgvP76RpYQ8PgtFI78Gf_ubQDc8FCeXNGQOXoMkKcmF_Is1mMh8gFrKHfWMlAYA5bP-65wZMeVp0rC11NovaVHkp_-y1Eg9KZIVYKLQAYVSFK4T6K4MQW6-pnLTdov8xoikSVct7Ip_JYj0FM9bvN9KXPTcxPQaCgYKAWoSARISFQHGX2MikUp7DJdMhrHJdZf90mATHw0169');

            // echo '<pre>';
            // print_r($user);
            // echo '</pre>';

            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                $newUser = new User();
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->password = Hash::make(str_replace('@gmail.com', '', $user->email));
                $newUser->save();

                Auth::login($newUser);
            }

            if (auth()->check()) {
                return response()->json([
                    'message' => 'Logged in successfully',
                    'data' => ['token' => auth()->user()->createToken('Google Auth API Token')->plainTextToken],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("error:" . $e->getMessage() . $e->getLine());

            return response()->json([
                'error' => 'Failed to Login try again',
                'code' => 401
            ]);
        }
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->userFromToken('EAAVmBX4J3xABO4Er3VaNoGtP6XEEeUyl2aeoVKGjai8xs7UjL8xl10spmKndY3FgRgR6JLxIWvgRAmo1yhZAYz20cng5RkmjbeZC52qA5Xl4qRpZAZB4lMcz0kxMZBKXASJlr1rIZCpyjZAALS8WrWk2f8HZCt5itZCOOClubPFKOQrgexQ1TWxCDPWGHUhOXxDhgxzbTNYReM8Yl7ZBdE7ShpwUYZC5H9aDF8PjROCXOq6BQ5F8FWf17FzS0ZAAQTbPZBCiaZCwZDZD');


            echo '<pre>';
            print_r($facebookUser);
            echo '</pre>';

            return;
            $existingUser = User::where('email', $facebookUser->email)->first();

            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                $newUser = new User();
                $newUser->name = $facebookUser->name;
                $newUser->email = $facebookUser->email;
                $newUser->password = Hash::make(str_replace('@gmail.com', '', $facebookUser->email));

                $newUser->save();
                Auth::login($newUser);
            }

            if (auth()->check()) {
                return response()->json([
                    'message' => 'Logged in successfully',
                    'data' => ['token' => auth()->user()->createToken('Facebook Auth API Token')->plainTextToken],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("error:" . $e->getMessage() . $e->getLine());

            return response()->json([
                'error' => 'Failed to Login try again',
                'code' => 401
            ]);
        }
    }

    public function logout(Request $request)
    {
        // Check if the request is authenticated
        if ($request->user()) {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }
}
