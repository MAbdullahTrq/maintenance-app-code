<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TechnicianWelcomeMail;
use Illuminate\Support\Facades\Password;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = User::where('invited_by', auth()->id())
            ->with('role')
            ->get();

        return view('technicians.index', compact('technicians'));
    }

    public function create()
    {
        return view('technicians.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $role = Role::where('slug', 'technician')->first();

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make('password'), // Temporary password, will be changed via verification
            'invited_by' => auth()->id(),
            'role_id' => $role->id,
            'is_active' => true,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('technician-images', 'public');
            $userData['image'] = $imagePath;
        }

        $technician = User::create($userData);

        // Generate verification token and send welcome email
        $verificationToken = $technician->generateVerificationToken();
        $manager = auth()->user();
        
        try {
            Mail::to($technician->email)->send(new TechnicianWelcomeMail($technician, $manager, $verificationToken));
            $successMessage = 'Technician created successfully! A welcome email with account verification link has been sent to ' . $technician->email;
        } catch (\Exception $e) {
            // If email fails, still show success but mention email issue
            $successMessage = 'Technician created successfully! However, there was an issue sending the welcome email. Please contact the technician directly.';
        }

        return redirect()->route('technicians.index')
            ->with('success', $successMessage);
    }

    public function edit(User $user)
    {
        abort_if($user->invited_by !== auth()->id(), 403);
        return view('technicians.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->invited_by !== auth()->id(), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image) {
                \Illuminate\Support\Facades\Storage::delete('public/' . $user->image);
            }
            
            $imagePath = $request->file('image')->store('technician-images', 'public');
            $userData['image'] = $imagePath;
        }

        $user->update($userData);

        return redirect()->route('technicians.index')
            ->with('success', 'Technician updated successfully!');
    }

    public function destroy(User $user)
    {
        abort_if($user->invited_by !== auth()->id(), 403);
        $user->delete();

        return redirect()->route('technicians.index')
            ->with('success', 'Technician deleted successfully!');
    }

    public function toggleActive(User $user)
    {
        abort_if($user->invited_by !== auth()->id(), 403);
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('technicians.index')
            ->with('success', 'Technician status updated successfully!');
    }

    public function resetPassword(User $user)
    {
        abort_if($user->invited_by !== auth()->id(), 403);
        
        // Send password reset email
        $status = Password::sendResetLink(
            ['email' => $user->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('technicians.index')
                ->with('success', "Password reset email sent successfully to {$user->email}! The technician will receive an email with instructions to reset their password.");
        } else {
            return redirect()->route('technicians.index')
                ->with('error', 'There was an error sending the password reset email. Please try again.');
        }
    }
}
