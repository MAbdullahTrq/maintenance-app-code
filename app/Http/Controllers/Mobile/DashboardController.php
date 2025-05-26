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
        $properties = Property::where('manager_id', $user->id)->get();
        $technicians = User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->get();
        $pendingRequests = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->where('status', 'pending')->get();
        $requestsCount = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        return view('mobile.dashboard', [
            'properties' => $properties,
            'technicians' => $technicians,
            'pendingRequests' => $pendingRequests,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function allRequests()
    {
        $user = Auth::user();
        // Only allow property managers
        if (!$user || !$user->isPropertyManager()) {
            abort(403);
        }
        $properties = Property::where('manager_id', $user->id)->get();
        $allRequests = MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->latest()->get();
        $propertiesCount = $properties->count();
        $techniciansCount = User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = $allRequests->count();
        // Count by status
        $declinedCount = $allRequests->where('status', 'declined')->count();
        $assignedCount = $allRequests->where('status', 'assigned')->count();
        $acceptedCount = $allRequests->where('status', 'accepted')->count();
        $startedCount = $allRequests->where('status', 'started')->count();
        $completedCount = $allRequests->where('status', 'completed')->count();
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
        ]);
    }
}
