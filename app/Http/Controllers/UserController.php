<?php

namespace App\Http\Controllers;

// all data from form
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function profile(User $user)
    {
        return view('user-profile', ['username' => $user->username, 'products' => $user->products()->latest()->get(), 'productCount' => $user->products()->count()]);
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', ' You are now logged out');
    }

    public function login(Request $request, User $user)
    {
        $incomingFields = $request->validate(
            [
                'loginusername' => 'required',
                'loginpassword' => 'required'
            ]);
            // auth returns object and we match data
        if( auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']]))
        {
            // session object
            $request->session()->regenerate(); // user is saved as coockie, sends it on every request
            // redirect here with following messege
            // return redirect('/')->with('success', 'You have successfully loged in to PopArt Market');
            session(['cart' => []]);
            // create cart array
            return view('user-profile', ['username' => $user->username, 'products' => $user->products()->latest()->get(), 'productCount' => $user->products()->count()]);
        }
        else
        {
            return redirect('/')->with('failure', 'Invalid login.');
        }
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate(
            [
                // username has to be filled, min 3 letters, max 20, unique username
                'username' => ['required', 'min:3', 'max:20', Rule::unique('users','username')],
                'email' => ['required', 'email', Rule::unique('users','email')],
                // min 6 letter, have to confirm it -> retaype
                'password' => ['required', 'min:6', 'confirmed']
            ]);


            // hashing passwrod with bcrypt
            $incomingFields['password'] = bcrypt($incomingFields['password']) ;

        // new user item
        $user = User::create($incomingFields);

        // login after register
        auth()->login($user);

        return redirect('/')->with('success','Thank you for joining grocery shop');
    }

    public function showCorrectHomepage()
    {

        $products = Product::paginate(5); // 5 products per page
        return view('homepage-feed', compact('products'));


    }

    public function updateProfile(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore(auth()->id())],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'email.unique' => 'The email has already been taken.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Passwords do not match.'
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Update email if provided
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Save the changes to the user
        $user->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

// Admin -------------- Admin ---------- Admin ----------
    public function adminPage(User $user)
    {
        $users = User::all();
        $user = auth()->user();
        if(!isset($user->isAdmin))
        {
            return redirect('/')->with('failure','Access denied, you are not an administrator');;
        }
        if($user->isAdmin === 1)
        {
            return view('admins-only', ['users' => $users]);
        }
            return redirect('/')->with('failure','Access denied, you are not an administrator');;
        // return 'You are admmin';
    }

    public function deleteUser(User $user)
    {
        if(!(auth()->user()->isAdmin === 1))
        {
            return redirect('/homepage')->with('failure','Warning ! U are not admin !');
        }
        $user->delete();
        return redirect('/admins-only')->with('success','User successfuly deleted');
    }

    public function viewUser(User $user)
    {
        return view('edit-user', ['user' => $user]);
    }

    public function update(User $user, Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $incomingFields['username'] = strip_tags($incomingFields['username']);
        $incomingFields['email'] = strip_tags($incomingFields['email']);

        // Update username and email
        $user->update([
            'username' => $incomingFields['username'],
            'email' => $incomingFields['email'],
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($incomingFields['password']);
            $user->save();
        }

        return back()->with('success', 'User details updated successfully.');
    }
}
