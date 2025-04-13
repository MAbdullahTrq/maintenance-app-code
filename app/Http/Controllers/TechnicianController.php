<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        $password = Str::random(10);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($password),
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

        return redirect()->route('technicians.index')
            ->with('success', "Technician created successfully! Their temporary password is: {$password}");
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
        $password = Str::random(10);
        
        $user->update([
            'password' => Hash::make($password)
        ]);

        return redirect()->route('technicians.index')
            ->with('password_reset', "Password reset successfully! New temporary password is: {$password}");
    }
}
