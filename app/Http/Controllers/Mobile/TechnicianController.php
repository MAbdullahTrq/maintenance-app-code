<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TechnicianStartedNotification;
use App\Mail\TechnicianCompletedNotification;
use App\Mail\TechnicianStartedRequesterNotification;
use App\Mail\TechnicianCompletedRequesterNotification;
use App\Mail\TechnicianWelcomeMail;
use Illuminate\Support\Facades\Password;

class TechnicianController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $technicians = User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $workspaceOwner->id)->get();
        $propertiesCount = \App\Models\Property::where('manager_id', $workspaceOwner->id)->count();
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $workspaceOwner->id)->pluck('id'))->count();
        
        // Get team members count (excluding technicians)
        $teamMembersCount = \App\Models\User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['team_member', 'viewer', 'editor']);
            })
            ->count();
        
        return view('mobile.technicians', [
            'technicians' => $technicians,
            'techniciansCount' => $technicians->count(),
            'propertiesCount' => $propertiesCount,
            'ownersCount' => $ownersCount,
            'requestsCount' => $requestsCount,
            'teamMembersCount' => $teamMembersCount,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $propertiesCount = \App\Models\Property::where('manager_id', $workspaceOwner->id)->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $workspaceOwner->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $workspaceOwner->id)->pluck('id'))->count();
        
        // Get team members count (excluding technicians)
        $teamMembersCount = \App\Models\User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['team_member', 'viewer', 'editor']);
            })
            ->count();
        
        return view('mobile.technician_create', compact('propertiesCount', 'techniciansCount', 'requestsCount', 'teamMembersCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user = auth()->user();
        // For team members, use the workspace owner's ID
        $managerId = $user->isTeamMember() ? $user->getWorkspaceOwner()->id : $user->id;
        
        $newUser = new \App\Models\User();
        $newUser->name = $request->name;
        $newUser->email = $request->email;
        $newUser->phone = $request->phone;
        $newUser->password = bcrypt('password'); // Temporary password, will be changed via verification
        $newUser->invited_by = $managerId;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('technician-profiles', 'public');
            $newUser->image = $path;
        }
        
        // Get the technician role and assign it
        $technicianRole = \App\Models\Role::where('slug', 'technician')->first();
        $newUser->role_id = $technicianRole->id;
        $newUser->save();

        // Generate verification token and send welcome email
        $verificationToken = $newUser->generateVerificationToken();
        $manager = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        try {
            Mail::to($newUser->email)->send(new TechnicianWelcomeMail($newUser, $manager, $verificationToken));
            $successMessage = 'Technician added successfully! A welcome email with account verification link has been sent to ' . $newUser->email;
        } catch (\Exception $e) {
            // If email fails, still show success but mention email issue
            $successMessage = 'Technician added successfully! However, there was an issue sending the welcome email. Please contact the technician directly.';
        }

        return redirect()->route('mobile.technicians.index')->with('success', $successMessage);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
        ]);
        $technician = User::findOrFail($id);
        $technician->name = $request->name;
        $technician->email = $request->email;
        $technician->phone = $request->phone;
        $technician->save();
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician updated successfully.');
    }

    public function destroy($id)
    {
        $technician = User::findOrFail($id);
        $technician->delete();
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician deleted successfully.');
    }

    public function edit($id)
    {
        $technician = User::findOrFail($id);
        $user = auth()->user();
        $propertiesCount = \App\Models\Property::where('manager_id', $user->id)->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $user->id)->pluck('id'))->count();
        return view('mobile.edit_technician', [
            'technician' => $technician,
            'propertiesCount' => $propertiesCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function show($id)
    {
        $technician = User::findOrFail($id);
        // Get all properties managed by the current manager
        $user = auth()->user();
        $properties = \App\Models\Property::where('manager_id', $user->id)->get();
        // Get maintenance requests assigned to this technician for these properties
        $maintenanceRequests = \App\Models\MaintenanceRequest::where('assigned_to', $technician->id)
            ->whereIn('property_id', $properties->pluck('id'))
            ->with('property')
            ->get();
        $propertiesCount = $properties->count();
        $techniciansCount = User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        return view('mobile.technician_show', [
            'technician' => $technician,
            'maintenanceRequests' => $maintenanceRequests,
            'propertiesCount' => $propertiesCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user();
        $sortBy = request('sort', 'created_at'); // Default sort by date
        $sortDirection = request('direction', 'desc'); // Default descending
        
        // Validate sort parameters
        $allowedSortColumns = ['created_at', 'status'];
        $allowedDirections = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        
        if (!in_array($sortDirection, $allowedDirections)) {
            $sortDirection = 'desc';
        }
        
        // Assigned: status = 'assigned'
        $assignedRequests = \App\Models\MaintenanceRequest::where('assigned_to', $user->id)
            ->where('status', 'assigned')
            ->with('property')
            ->orderBy($sortBy, $sortDirection)
            ->get();
        // Accepted: status = 'accepted', 'acknowledged', or 'started'
        $acceptedRequests = \App\Models\MaintenanceRequest::where('assigned_to', $user->id)
            ->whereIn('status', ['accepted', 'acknowledged', 'started'])
            ->with('property')
            ->orderBy($sortBy, $sortDirection)
            ->get();
        // Completed: status = 'completed'
        $completedRequests = \App\Models\MaintenanceRequest::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->with('property')
            ->orderBy($sortBy, $sortDirection)
            ->get();
        return view('mobile.technician_dashboard', [
            'assignedRequests' => $assignedRequests,
            'acceptedRequests' => $acceptedRequests,
            'completedRequests' => $completedRequests,
            'assignedCount' => $assignedRequests->count(),
            'acceptedCount' => $acceptedRequests->count(),
            'completedCount' => $completedRequests->count(),
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function showRequest($id)
    {
        $user = Auth::user();
        $request = \App\Models\MaintenanceRequest::with(['property', 'images', 'comments', 'property.manager'])
            ->where('id', $id)
            ->where('assigned_to', $user->id)
            ->firstOrFail();
        return view('mobile.technician_request_show', [
            'request' => $request,
            'property' => $request->property,
            'requester' => [
                'name' => $request->requester_name,
                'email' => $request->requester_email,
                'phone' => $request->requester_phone,
            ],
            'comments' => $request->comments,
        ]);
    }

    public function acceptRequest($id)
    {
        $user = Auth::user();
        $request = \App\Models\MaintenanceRequest::where('id', $id)->where('assigned_to', $user->id)->firstOrFail();
        $request->status = 'accepted';
        $request->save();
        return redirect()->route('mobile.technician.request.show', $id)->with('success', 'Request accepted.');
    }

    public function declineRequest($id)
    {
        $user = Auth::user();
        $request = \App\Models\MaintenanceRequest::where('id', $id)->where('assigned_to', $user->id)->firstOrFail();
        $request->status = 'declined';
        $request->save();
        return redirect()->route('mobile.technician.dashboard')->with('success', 'Request declined.');
    }

    public function startRequest($id)
    {
        $user = Auth::user();
        $request = \App\Models\MaintenanceRequest::where('id', $id)->where('assigned_to', $user->id)->firstOrFail();
        $request->status = 'started';
        $request->started_at = now();
        $request->save();

        // Send notifications to manager and requester
        Mail::to($request->property->manager->email)
            ->send(new TechnicianStartedNotification($request));

        if ($request->requester_email) {
            Mail::to($request->requester_email)
                ->send(new TechnicianStartedRequesterNotification($request));
        }

        return redirect()->route('mobile.technician.request.show', $id)->with('success', 'Work started.');
    }

    public function finishRequest($id)
    {
        $user = Auth::user();
        $request = \App\Models\MaintenanceRequest::where('id', $id)->where('assigned_to', $user->id)->firstOrFail();
        $request->status = 'completed';
        $request->completed_at = now();
        $request->save();

        // Send notifications to manager and requester
        Mail::to($request->property->manager->email)
            ->send(new TechnicianCompletedNotification($request));

        if ($request->requester_email) {
            Mail::to($request->requester_email)
                ->send(new TechnicianCompletedRequesterNotification($request));
        }

        return redirect()->route('mobile.technician.request.show', $id)->with('success', 'Work finished.');
    }

    public function deactivate($id)
    {
        $user = auth()->user();
        $technician = \App\Models\User::where('id', $id)->where('invited_by', $user->id)->firstOrFail();
        $technician->is_active = false;
        $technician->save();
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician deactivated successfully.');
    }

    public function activate($id)
    {
        $user = auth()->user();
        $technician = \App\Models\User::where('id', $id)->where('invited_by', $user->id)->firstOrFail();
        $technician->is_active = true;
        $technician->save();
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician activated successfully.');
    }

    public function resetPassword($id)
    {
        $user = auth()->user();
        $technician = \App\Models\User::where('id', $id)->where('invited_by', $user->id)->firstOrFail();
        
        // Send password reset email
        $status = Password::sendResetLink(
            ['email' => $technician->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('mobile.technicians.index')
                ->with('success', "Password reset email sent successfully to {$technician->email}! The technician will receive an email with instructions to reset their password.");
        } else {
            return redirect()->route('mobile.technicians.index')
                ->with('error', 'There was an error sending the password reset email. Please try again.');
        }
    }
}
