<?php

namespace App\Http\Controllers;

use App\Mail\Websitemail;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function login()
    {
        return view('admin.login');
    }
    public function loginSubmit(Request $request)
    {
        // 1 Validate input
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2 Attempt admin login
        if (Auth::guard('admin')->attempt($validatedData)) {

            // 3 Prevent session fixation
            $request->session()->regenerate();

            // 4 Redirect to intended page
            return redirect()->intended(route('admin.dashboard'));
        }

        // 5 Authentication failed
        return back()->withErrors([
            'email' => 'Invalid admin credentials',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Logged out successfully');
    }
    public function forgotPassword()
    {
        return view('admin.forget_password');
    }
    public function forgotPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return redirect()->back()->with('error', 'Email not Found');
        }
        $token = hash('sha256', time());
        $admin->token = $token;
        $admin->update();

        $link = route('admin.reset.password', [$token, $request->email]);
        $subject = 'Reset Password';
        $message = 'Click on the following link to Reset your Password: </br>';
        $message .= '<a href="' . $link . '">' . $link . '</a>';

        Mail::to($request->email)->send(new Websitemail($subject, $message));
        return redirect()->back()->with('success', 'Reset Password link send to your Mail');
    }

    public function resetPassword($token, $email)
    {
        $admin = Admin::where('email', $email)->where('token', $token)->first();
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Invalid Eamil or token');
        }
        return view('admin.reset_password', ['token' => $token, 'email' => $email]);
    }
    public function resetPasswordSubmit(Request $request, $token, $email)
    {
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        $admin = Admin::where('email', $email)->where('token', $token)->first();
        $admin->password = Hash::make($request->password);
        $admin->token = '';
        $admin->update();

        return redirect()->route('admin.login')->with('success', 'Password reset Successfully');
    }
    public function profile()
    {
        return view('admin.profile');
    }
    public function profileSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:admins,email,' . Auth::guard('admin')->user()->id,
        ]);
        $admin = Admin::where('id', Auth::guard('admin')->user()->id)->first();

        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            $final_name = 'admin_' . time() . '.' . $request->photo->extension();
            if ($admin->photo != '') {
                unlink(public_path('uploads/' . $admin->photo));
            }
            $request->photo->move(public_path('uploads'), $final_name);
            $admin->photo = $final_name;
        }

        if ($request->password) {
            $request->validate([
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);
            $admin->password = Hash::make($request->password);
        }

        $admin->name = $request->name;
        $admin->email = $request->email;

        $admin->update();
        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
