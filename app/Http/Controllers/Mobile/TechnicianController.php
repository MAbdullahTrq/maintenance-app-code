<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TechnicianController extends Controller
{
    /**
     * Display a listing of technicians.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $technicians = User::whereHas('roles', function($query) {
                $query->where('name', 'technician');
            })
            ->orderBy('name')
            ->get();
            
        return view('mobile.technicians.index', compact('technicians'));
    }
    
    /**
     * Display the specified technician.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Verify the user is a technician
        if (!$user->isTechnician()) {
            abort(404);
        }
        
        $assignedRequests = MaintenanceRequest::where('technician_id', $user->id)
            ->with('property')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mobile.technicians.show', compact('user', 'assignedRequests'));
    }
    
    /**
     * Show the form for editing the specified technician.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Verify the user is a technician
        if (!$user->isTechnician()) {
            abort(404);
        }
        
        return view('mobile.technicians.edit', compact('user'));
    }
    
    /**
     * Update the specified technician in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Verify the user is a technician
        if (!$user->isTechnician()) {
            abort(404);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'active' => 'required|boolean',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('mobile.technicians.show', $user)
            ->with('success', 'Technician updated successfully');
    }
    
    /**
     * Reset the technician's password.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(User $user)
    {
        // Verify the user is a technician
        if (!$user->isTechnician()) {
            abort(404);
        }
        
        // Generate a random password
        $password = Str::random(10);
        
        // Update user's password
        $user->password = Hash::make($password);
        $user->save();
        
        // TODO: In a real system, send email to the user with the new password
        
        return redirect()->back()
            ->with('success', 'Password reset successfully. New password: ' . $password);
    }
    
    /**
     * Remove the specified technician from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Verify the user is a technician
        if (!$user->isTechnician()) {
            abort(404);
        }
        
        // Check if technician has any assigned maintenance requests
        $hasAssignedRequests = MaintenanceRequest::where('technician_id', $user->id)
            ->whereIn('status', ['assigned', 'acknowledged', 'started'])
            ->exists();
            
        if ($hasAssignedRequests) {
            return redirect()->back()
                ->with('error', 'Cannot delete technician with active assignments. Reassign or complete all tasks first.');
        }
        
        $user->delete();
        
        return redirect()->route('mobile.technicians.index')
            ->with('success', 'Technician deleted successfully');
    }
} 