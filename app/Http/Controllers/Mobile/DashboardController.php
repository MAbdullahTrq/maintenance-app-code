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
        // Only allow property managers
        if (!$user || !$user->isPropertyManager()) {
            abort(403);
        }
        $hasActiveSubscription = method_exists($user, 'hasActiveSubscription') ? $user->hasActiveSubscription() : false;
        $properties = Property::where('manager_id', $user->id)->get();
        $technicians = User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->get();
        $pendingRequests = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->where('status', 'pending')->get();
        $requestsCount = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        return view('mobile.dashboard', [
            'properties' => $properties,
            'technicians' => $technicians,
            'pendingRequests' => $pendingRequests,
            'requestsCount' => $requestsCount,
            'hasActiveSubscription' => $hasActiveSubscription,
        ]);
    }

    public function allRequests(Request $request)
    {
        $user = Auth::user();
        // Only allow property managers
        if (!$user || !$user->isPropertyManager()) {
            abort(403);
        }
        $properties = Property::where('manager_id', $user->id)->get();
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
        $techniciansCount = User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = $allRequests->count();
        // Count by status (for tabs)
        $allRequestsForCount = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->get();
        $declinedCount = $allRequestsForCount->where('status', 'declined')->count();
        $assignedCount = $allRequestsForCount->where('status', 'assigned')->count();
        $acceptedCount = $allRequestsForCount->where('status', 'accepted')->count();
        $startedCount = $allRequestsForCount->where('status', 'started')->count();
        $completedCount = $allRequestsForCount->where('status', 'completed')->count();
        return view('mobile.all_requests', [
            'allRequests' => $allRequests,
            'propertiesCount' => $propertiesCount,
            'techniciansCount' => $techniciansCount,
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
