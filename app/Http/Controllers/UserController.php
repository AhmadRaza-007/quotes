<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        try {
            // return User::create(['name' => 'admin', 'email' => $request->email, 'password' => Hash::make($request->password)]);
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                'user_type' => 1,
            ];
            if (Auth::attempt($credentials)) {
                return redirect()->route('admin.dashboard');
            } else {
                return back()->withInput();
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function redirectToGoogleProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

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

            return redirect()->route('admin.dashboard');
        } catch (\Exception $e) {
            \Log::error("error:" . $e->getMessage() . $e->getLine());
            return redirect()->route('admin.login')->with('error', 'Unable to login with Google.');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->setScopes(['email', 'public_profile'])
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            // echo '<pre>';
            // print_r($facebookUser);
            // echo '</pre>';

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

            return redirect()->route('admin.dashboard');
        } catch (\Exception $e) {
            \Log::error("error:" . $e->getMessage() . $e->getLine());
            return redirect()->route('admin.login')->with('error', 'Unable to login with Facebook.');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
