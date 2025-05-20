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
        return view('mobile.dashboard', [
            'properties' => $properties,
            'technicians' => $technicians,
            'pendingRequests' => $pendingRequests,
        ]);
    }
}
