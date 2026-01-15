<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\Websitemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('user.dashboard');
    }
    public function login()
    {
        return view('user.login');
    }
    public function register()
    {
        return view('user.register');
    }
    public function registerSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        $token = hash('sha256', time());

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->token = $token;

        $user->save();
        $link = route('register.verify', [$token, $request->email]);
        $subject = 'Registration Verification';
        $message = 'Click on the following link to verify your email: </br>';
        $message .= '<a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));
        return redirect()->route('login')->with('success', 'Registration Successfull you can log in now.');
    }
    public function registerVerify($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid Link or Email');
        }
        $user->token = '';
        $user->status = 1;
        $user->update();

        return redirect()->route('login')->with('success', 'Email verified successfull. You can login now');
    }
    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $check = $request->all();
        $data = [
            'email' => $check['email'],
            'password' => $check['password'],
        ];

        if (Auth::guard('web')->attempt($data)) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->back()->with('error', 'Invalid Credentials');
        }
    }
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
    public function forgetPassword()
    {
        return view('user.forget_password');
    }
    public function forgetPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required'
        ]);

        $admin = User::where('email', $request->email)->first();
        if (!$admin) {
            return redirect()->back()->with('error', 'Email not Found');
        }
        $token = hash('sha256', time());
        $admin->token = $token;
        $admin->update();

        $link = route('reset.password', [$token, $request->email]);
        $subject = 'Reset Password';
        $message = 'Click on the following link to Reset your Password: </br>';
        $message .= '<a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));
        return redirect()->back()->with('success', 'Reset Password link send to your Mail');
    }

    public function resetPassword($token, $email)
    {
        $admin = User::where('email', $email)->where('token', $token)->first();
        if (!$admin) {
            return redirect()->route('login')->with('error', 'Invalid Eamil or token');
        }
        return view('user.reset_password', ['token' => $token, 'email' => $email]);
    }
    public function resetPasswordSubmit(Request $request, $token, $email)
    {
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        $admin = User::where('email', $email)->where('token', $token)->first();
        $admin->password = Hash::make($request->password);
        $admin->token = '';
        $admin->update();

        return redirect()->route('login')->with('success', 'Password reset Successfully');
    }
    public function profile()
    {
        return view('user.profile');
    }
    public function profileSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . Auth::guard('web')->user()->id,
        ]);
        $user = User::where('id', Auth::guard('web')->user()->id)->first();

        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            $final_name = 'user_' . time() . '.' . $request->photo->extension();
            if ($user->photo != '') {
                unlink(public_path('uploads/' . $user->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $user->photo = $final_name;
        }

        if ($request->password) {
            $request->validate([
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->zip = $request->zip;


        $user->update();
        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
