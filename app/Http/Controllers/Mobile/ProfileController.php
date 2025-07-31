<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Get workspace owner for team members
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        // Stats for nav bar
        $propertiesCount = $workspaceOwner->managedProperties()->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $workspaceOwner->managedProperties()->pluck('id'))->count();
        $ownersCount = \App\Models\Owner::where('manager_id', $workspaceOwner->id)->count();
        
        return view('mobile.profile', compact('user', 'propertiesCount', 'techniciansCount', 'requestsCount', 'ownersCount'));
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Increased to 5MB
        ]);
        
        $user = Auth::user();
        
        if ($request->hasFile('image')) {
            // Use image optimization service
            $imageOptimizationService = new ImageOptimizationService();
            $imagePath = $imageOptimizationService->optimizeAndResize(
                $request->file('image'), 
                'profile-images', 
                400, // Width for profile pictures
                400  // Height for profile pictures
            );
            
            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            
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