<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Notification;
use Illuminate\Support\Facades\View;
class UserController extends Controller
{
    public function profile()
    {
        return view('user-profile');
    }

    public function logout()
    {
        session()->forget('cart');
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out');
    }

    //treba kreirati posebnu stranicu za login i ovo refaktorisati
    public function login(Request $request, User $user)
    {
        $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        $loginusername = $request->input('loginusername');
        $loginpassword = $request->input('loginpassword');

        if (empty($loginusername) || empty($loginpassword)) {
            return redirect('/')->with('failure', 'Username or password cannot be empty.');
        }

        if (auth()->attempt(['username' => $loginusername, 'password' => $loginpassword])) {
            $request->session()->regenerate();
            session(['cart' => []]);
            return redirect()->intended(route('profile'))->with('success', 'You are now logged in');
        } else {
            return redirect('/')->with('failure', 'Invalid login.');
        }
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6', 'confirmed']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);

        auth()->login($user);

        return redirect('/')->with('success', 'Thank you for joining grocery shop');
    }

    //Treba promeniti ime ove metode i kasnije dodati jos jednu rutu za / kada se doda login str
    public function showCorrectHomepage()
    {
        $products = Product::paginate(5);
        return view('homepage-feed', compact('products'));
    }

    public function updateProfile(Request $request)
    {

        $request->validate([
            'password' => ['required', 'nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $user = auth()->user();

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function viewOrders()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest()->get();
        return view('user-orders', compact('orders'));
    }

// Admin -------------- Admin ---------- Admin ----------
    public function adminPage(User $user)
    {
        $users = User::all();

            return view('admin-dashboard', ['users' => $users]); // No need to set status code for views
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect('/admin-dashboard')->with('success', 'User successfully deleted');
    }

    public function viewUser(User $user)
    {
        return view('edit-user', ['user' => $user]); // No need to set status code for views
    }

    public function update(User $user, Request $request)
    {
        $incomingFields = $request->validate([
            'username' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|min:6|confirmed',
        ]);

        $incomingFields['username'] = strip_tags($incomingFields['username']);
        $incomingFields['email'] = strip_tags($incomingFields['email']);

        // Check if the provided username already exists for another user
        if ($user->username !== $incomingFields['username'] && User::where('username', $incomingFields['username'])->exists()) {
            return back()->with('failure', 'Username already exists.')->setStatusCode(409); // Conflict
        }

        // Check if the provided email already exists for another user
        if ($user->email !== $incomingFields['email'] && User::where('email', $incomingFields['email'])->exists()) {
            return back()->with('failure', 'Email already exists.')->setStatusCode(409); // Conflict
        }

        try {
            $user->update([
                'username' => $incomingFields['username'],
                'email' => $incomingFields['email'],
            ]);

            if ($request->filled('password')) {
                $user->password = bcrypt($incomingFields['password']);
                $user->save();
            }

            return back()->with('success', 'User details updated successfully.');
        } catch (QueryException $e) {
            // If any other database error occurs, return a generic error message
            return back()->with('failure', 'An error occurred while updating user details.')->setStatusCode(500); // Internal Server Error
        }
    }

}
