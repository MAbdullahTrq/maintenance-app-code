<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TechnicianController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $technicians = User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->get();
        $propertiesCount = \App\Models\Property::where('manager_id', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $user->id)->pluck('id'))->count();
        return view('mobile.technicians', [
            'technicians' => $technicians,
            'techniciansCount' => $technicians->count(),
            'propertiesCount' => $propertiesCount,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
        ]);
        $technician = new User();
        $technician->name = $request->name;
        $technician->email = $request->email;
        $technician->phone = $request->phone;
        $technician->invited_by = $user->id;
        $technician->password = bcrypt('password'); // Default password, should be changed
        $technician->save();
        // Assign technician role
        $technician->role_id = \App\Models\Role::where('slug', 'technician')->first()->id;
        $technician->save();
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician added successfully.');
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
        // Assigned: status = 'assigned'
        $assignedRequests = \App\Models\MaintenanceRequest::where('assigned_to', $user->id)
            ->where('status', 'assigned')
            ->with('property')
            ->get();
        // Accepted: status = 'accepted', 'acknowledged', or 'started'
        $acceptedRequests = \App\Models\MaintenanceRequest::where('assigned_to', $user->id)
            ->whereIn('status', ['accepted', 'acknowledged', 'started'])
            ->with('property')
            ->get();
        // Completed: status = 'completed'
        $completedRequests = \App\Models\MaintenanceRequest::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->with('property')
            ->get();
        return view('mobile.technician_dashboard', [
            'assignedRequests' => $assignedRequests,
            'acceptedRequests' => $acceptedRequests,
            'completedRequests' => $completedRequests,
            'assignedCount' => $assignedRequests->count(),
            'acceptedCount' => $acceptedRequests->count(),
            'completedCount' => $completedRequests->count(),
        ]);
    }

    public function showRequest($id)
    {
        $user = Auth::user();
        $request = \App\Models\MaintenanceRequest::with(['property', 'images'])
            ->where('id', $id)
            ->where('assigned_to', $user->id)
            ->firstOrFail();
        return view('mobile.technician_request_show', [
            'request' => $request,
        ]);
    }
}
