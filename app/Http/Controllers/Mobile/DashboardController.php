<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\User;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Allow property managers and team members
        if (!$user || (!$user->isPropertyManager() && !$user->hasTeamMemberRole())) {
            abort(403);
        }
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $hasActiveSubscription = method_exists($user, 'hasActiveSubscription') ? $user->hasActiveSubscription() : false;
        $properties = $workspaceOwner->managedProperties()->get();
        $owners = $workspaceOwner->managedOwners()->get();
        $technicians = User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($q) { 
                $q->where('slug', 'technician'); 
            })
            ->get();
        $pendingRequests = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->where('status', 'pending')->get();
        $requestsCount = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        
        // Get team members count (excluding technicians)
        $teamMembersCount = User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['team_member', 'viewer', 'editor']);
            })
            ->count();
        
        return view('mobile.dashboard', [
            'properties' => $properties,
            'owners' => $owners,
            'ownersCount' => $owners->count(),
            'technicians' => $technicians,
            'pendingRequests' => $pendingRequests,
            'requestsCount' => $requestsCount,
            'hasActiveSubscription' => $hasActiveSubscription,
            'teamMembersCount' => $teamMembersCount,
        ]);
    }

    public function allRequests(Request $request)
    {
        $user = Auth::user();
        // Allow property managers and team members
        if (!$user || (!$user->isPropertyManager() && !$user->hasTeamMemberRole())) {
            abort(403);
        }
        
        // For team members, get the workspace owner's data
        $workspaceOwner = $user->isTeamMember() ? $user->getWorkspaceOwner() : $user;
        
        $properties = $workspaceOwner->managedProperties()->get();
        $status = $request->query('status');
        $sortBy = $request->query('sort', 'created_at'); // Default sort by date
        $sortDirection = $request->query('direction', 'desc'); // Default descending
        
        // Validate sort parameters
        $allowedSortColumns = ['created_at', 'priority'];
        $allowedDirections = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        
        if (!in_array($sortDirection, $allowedDirections)) {
            $sortDirection = 'desc';
        }
        
        $query = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'));
        if ($status && in_array($status, ['declined', 'assigned', 'accepted', 'started', 'completed'])) {
            $query->where('status', $status);
        }
        
        // Handle priority sorting with custom order
        if ($sortBy === 'priority') {
            $allRequests = $query->get()->sortBy(function($request) {
                $priorityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
                return $priorityOrder[strtolower($request->priority)] ?? 0;
            });
            if ($sortDirection === 'desc') {
                $allRequests = $allRequests->reverse();
            }
            $allRequests = $allRequests->values(); // Reset keys
        } else {
            $allRequests = $query->orderBy($sortBy, $sortDirection)->get();
        }
        $propertiesCount = $properties->count();
        $ownersCount = $workspaceOwner->managedOwners()->count();
        $techniciansCount = User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($q) { 
                $q->where('slug', 'technician'); 
            })
            ->count();
        
        // Get team members count (excluding technicians)
        $teamMembersCount = User::where('invited_by', $workspaceOwner->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', ['team_member', 'viewer', 'editor']);
            })
            ->count();
        
        // Count by status (for tabs)
        $allRequestsForCount = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->get();
        $requestsCount = $allRequestsForCount->count(); // Always show total count, not filtered count
        $declinedCount = $allRequestsForCount->where('status', 'declined')->count();
        $assignedCount = $allRequestsForCount->where('status', 'assigned')->count();
        $acceptedCount = $allRequestsForCount->where('status', 'accepted')->count();
        $startedCount = $allRequestsForCount->where('status', 'started')->count();
        $completedCount = $allRequestsForCount->where('status', 'completed')->count();
        return view('mobile.all_requests', [
            'allRequests' => $allRequests,
            'propertiesCount' => $propertiesCount,
            'ownersCount' => $ownersCount,
            'techniciansCount' => $techniciansCount,
            'teamMembersCount' => $teamMembersCount,
            'requestsCount' => $requestsCount,
            'declinedCount' => $declinedCount,
            'assignedCount' => $assignedCount,
            'acceptedCount' => $acceptedCount,
            'startedCount' => $startedCount,
            'completedCount' => $completedCount,
            'selectedStatus' => $status,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }
}
