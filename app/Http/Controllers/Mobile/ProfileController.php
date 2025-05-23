<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('mobile.profile', compact('user'));
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $user = Auth::user();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profile-images', 'public');
            $user->image = $imagePath;
            $user->save();
        }
        return redirect()->route('mobile.profile')->with('success', 'Profile picture updated!');
    }

    public function showChangePassword()
    {
        return view('mobile.change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);
        $user = Auth::user();
        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        return redirect()->route('mobile.profile')->with('success', 'Password changed successfully!');
    }
} 