<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return User::create(['name' => 'admin', 'email' => 'admin@admin.com', 'password' => Hash::make('123123123'), 'user_type' => 1]);
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        try {
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

    /**
     * Display a listing of users (Admin only)
     */
    public function userList(Request $request)
    {
        $users = User::query();

        // Search functionality
        if ($request->has('search')) {
            $users->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('id', 'like', '%' . $request->search . '%');
        }

        $users = $users->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {


        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'user_type' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing a user
     */
    public function edit($id)
    {


        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {


        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'user_type' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.users.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password changed successfully.');
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {


        // Prevent admin from deleting themselves
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
