<?php

namespace App\Http\Controllers;

use App\Models\TeamInvitation;
use App\Models\User;
use App\Models\Role;
use App\Mail\TeamInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    /**
     * Show the team management page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only property managers can manage teams
        if (!$user->isPropertyManager()) {
            abort(403);
        }

        $workspaceOwner = $user->getWorkspaceOwner();
        $teamMembers = $workspaceOwner->teamMembers()->with('role')->get();
        $pendingInvitations = $workspaceOwner->sentInvitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with('role')
            ->get();

        $availableRoles = Role::whereIn('slug', ['team_member', 'viewer', 'editor'])
            ->get();

        // Check if this is a mobile route
        if (request()->route()->getName() && str_starts_with(request()->route()->getName(), 'mobile.')) {
            return view('mobile.team.index', compact('teamMembers', 'pendingInvitations', 'availableRoles', 'workspaceOwner'));
        }

        return view('team.index', compact('teamMembers', 'pendingInvitations', 'availableRoles', 'workspaceOwner'));
    }

    /**
     * Show the form for inviting a new team member.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isPropertyManager()) {
            abort(403);
        }

        $roles = Role::whereIn('slug', ['team_member', 'viewer', 'editor'])
            ->get();

        // Check if this is a mobile route
        if (request()->route()->getName() && str_starts_with(request()->route()->getName(), 'mobile.')) {
            return view('mobile.team.create', compact('roles'));
        }

        return view('team.create', compact('roles'));
    }

    /**
     * Send a team invitation.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPropertyManager()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return back()->withErrors(['email' => 'A user with this email already exists.']);
        }

        // Check if invitation already exists
        $existingInvitation = TeamInvitation::where('email', $request->email)
            ->where('invited_by', $user->getWorkspaceOwner()->id)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return back()->withErrors(['email' => 'An invitation has already been sent to this email.']);
        }

        // Create invitation
        $invitation = TeamInvitation::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'invited_by' => $user->getWorkspaceOwner()->id,
        ]);

        // Send invitation email
        try {
            Mail::to($request->email)->send(new TeamInvitationMail($invitation));
        } catch (\Exception $e) {
            \Log::error('Failed to send team invitation email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send invitation email. Please try again.']);
        }

        // Check if this is a mobile route and redirect accordingly
        if (request()->route()->getName() && str_starts_with(request()->route()->getName(), 'mobile.')) {
            return redirect()->route('mobile.team.index')
                ->with('success', 'Team invitation sent successfully!');
        }

        return redirect()->route('team.index')
            ->with('success', 'Team invitation sent successfully!');
    }

    /**
     * Show the invitation acceptance page.
     */
    public function acceptInvitation($token)
    {
        $invitation = TeamInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with(['role', 'invitedBy'])
            ->firstOrFail();

        return view('team.accept', compact('invitation'));
    }

    /**
     * Process the invitation acceptance.
     */
    public function processInvitation(Request $request, $token)
    {
        $invitation = TeamInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => Hash::make($request->password),
            'role_id' => $invitation->role_id,
            'invited_by' => $invitation->invited_by,
            'is_active' => true,
        ]);

        // Mark invitation as accepted
        $invitation->accept($user);

        // Log the user in
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to the team! Your account has been created successfully.');
    }

    /**
     * Remove a team member.
     */
    public function removeMember(Request $request, $memberId)
    {
        $user = Auth::user();
        
        if (!$user->isPropertyManager()) {
            abort(403);
        }

        $workspaceOwner = $user->getWorkspaceOwner();
        $member = User::where('id', $memberId)
            ->where('invited_by', $workspaceOwner->id)
            ->firstOrFail();

        // Don't allow removing the workspace owner
        if ($member->id === $workspaceOwner->id) {
            return back()->withErrors(['error' => 'Cannot remove the workspace owner.']);
        }

        $member->delete();

        return back()->with('success', 'Team member removed successfully.');
    }

    /**
     * Cancel a pending invitation.
     */
    public function cancelInvitation(Request $request, $invitationId)
    {
        $user = Auth::user();
        
        if (!$user->isPropertyManager()) {
            abort(403);
        }

        $workspaceOwner = $user->getWorkspaceOwner();
        $invitation = TeamInvitation::where('id', $invitationId)
            ->where('invited_by', $workspaceOwner->id)
            ->whereNull('accepted_at')
            ->firstOrFail();

        $invitation->delete();

        return back()->with('success', 'Invitation cancelled successfully.');
    }

    /**
     * Update team member role.
     */
    public function updateRole(Request $request, $memberId)
    {
        $user = Auth::user();
        
        if (!$user->isPropertyManager()) {
            abort(403);
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $workspaceOwner = $user->getWorkspaceOwner();
        $member = User::where('id', $memberId)
            ->where('invited_by', $workspaceOwner->id)
            ->firstOrFail();

        // Don't allow changing the workspace owner's role
        if ($member->id === $workspaceOwner->id) {
            return back()->withErrors(['error' => 'Cannot change the workspace owner\'s role.']);
        }

        $member->update(['role_id' => $request->role_id]);

        return back()->with('success', 'Team member role updated successfully.');
    }

    /**
     * Update team member role (alias for mobile routes).
     */
    public function updateMemberRole(Request $request, $memberId)
    {
        return $this->updateRole($request, $memberId);
    }
}
