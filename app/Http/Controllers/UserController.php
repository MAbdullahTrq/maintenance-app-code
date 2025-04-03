<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            $users = User::with('role')->get();
        } else {
            $users = User::with('role')->where('id', auth()->id())->get();
        }
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Check if the route name starts with 'admin.'
        $isAdminRoute = str_starts_with(request()->route()->getName(), 'admin.');
        
        // Super managers can create any type of user
        if (auth()->user()->hasRole('admin')) {
            $roles = Role::all();
        } else {
            // Property managers can only create technicians
            $roles = Role::where('slug', 'technician')->get();
        }
        
        return view('users.create', compact('roles', 'isAdminRoute'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Check if the route name starts with 'admin.'
        $isAdminRoute = str_starts_with(request()->route()->getName(), 'admin.');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Super managers can create any type of user
        if (auth()->user()->hasRole('admin')) {
            // Allow any role
        } else {
            // Property managers can only create technicians
            $role = Role::find($validated['role_id']);
            if ($role->slug !== 'technician') {
                return redirect()->back()->withErrors(['role_id' => 'You can only create technician users.'])->withInput();
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'invited_by' => auth()->id(),
        ]);

        $redirectRoute = $isAdminRoute ? 'admin.users.index' : 'users.index';
        return redirect()->route($redirectRoute)->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Check if the route name starts with 'admin.'
        $isAdminRoute = str_starts_with(request()->route()->getName(), 'admin.');
        
        // Super managers can edit any user
        if (auth()->user()->hasRole('admin')) {
            $roles = Role::all();
        } else {
            // Property managers can only edit technicians they invited
            if ($user->invited_by !== auth()->id() && $user->id !== auth()->id()) {
                return redirect()->back()->with('error', 'You are not authorized to edit this user.');
            }
            
            // Only show technician role for property managers
            $roles = Role::where('slug', 'technician')->get();
        }
        
        return view('users.edit', compact('user', 'roles', 'isAdminRoute'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Check if the route name starts with 'admin.'
        $isAdminRoute = str_starts_with(request()->route()->getName(), 'admin.');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        // Super managers can update any user
        if (auth()->user()->hasRole('admin')) {
            // Allow any role
        } else {
            // Property managers can only update technicians they invited
            if ($user->invited_by !== auth()->id() && $user->id !== auth()->id()) {
                return redirect()->back()->with('error', 'You are not authorized to update this user.');
            }
            
            // Only allow technician role for property managers
            $role = Role::find($validated['role_id']);
            if ($role->slug !== 'technician' && $user->id !== auth()->id()) {
                return redirect()->back()->withErrors(['role_id' => 'You can only assign the technician role.'])->withInput();
            }
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ]);

        if ($request->filled('password') && $request->filled('password_confirmation')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $redirectRoute = $isAdminRoute ? 'admin.users.index' : 'users.index';
        return redirect()->route($redirectRoute)->with('success', 'User updated successfully.');
    }

    /**
     * Toggle the active status of the specified user.
     */
    public function toggleActive(User $user)
    {
        $this->authorize('update', $user);
        
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('users.index')
            ->with('success', 'User ' . $status . ' successfully.');
    }

    /**
     * Reset the password for the specified user.
     */
    public function resetPassword(User $user)
    {
        $this->authorize('update', $user);
        
        // Generate a random password
        $password = Str::random(10);

        $user->update([
            'password' => Hash::make($password),
        ]);

        // TODO: Send password reset email

        return redirect()->route('users.index')
            ->with('success', 'Password reset successfully. Temporary password: ' . $password);
    }

    /**
     * Show the form for changing the user's password.
     */
    public function showChangePasswordForm()
    {
        return view('users.change-password');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show the user profile page.
     */
    public function showProfile()
    {
        $user = Auth::user();
        
        return view('users.profile', compact('user'));
    }

    /**
     * Update the user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function destroy(User $user)
    {
        // Check if the route name starts with 'admin.'
        $isAdminRoute = str_starts_with(request()->route()->getName(), 'admin.');
        
        // Super managers can delete any user except themselves
        if (auth()->user()->hasRole('admin')) {
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }
        } else {
            // Property managers can only delete technicians they invited
            if ($user->invited_by !== auth()->id()) {
                return redirect()->back()->with('error', 'You are not authorized to delete this user.');
            }
            
            // Only allow deleting technicians
            if ($user->role->slug !== 'technician') {
                return redirect()->back()->with('error', 'You can only delete technician users.');
            }
        }

        $user->delete();

        $redirectRoute = $isAdminRoute ? 'admin.users.index' : 'users.index';
        return redirect()->route($redirectRoute)->with('success', 'User deleted successfully.');
    }
} 