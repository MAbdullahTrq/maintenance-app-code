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

    public function create()
    {
        $user = auth()->user();
        $propertiesCount = \App\Models\Property::where('manager_id', $user->id)->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $user->id)->pluck('id'))->count();
        return view('mobile.technician_create', compact('propertiesCount', 'techniciansCount', 'requestsCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $user = new \App\Models\User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt('password');
        $user->invited_by = auth()->id();

        // Set custom role_id for your app
        $technicianRole = \App\Models\Role::where('slug', 'technician')->first();
        if ($technicianRole) {
            $user->role_id = $technicianRole->id;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('technician-profiles', 'public');
            $user->image = $path;
        }
        $user->save();
        $user->assignRole('technician');
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician added! Default password is: password');
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
        return redirect()->route('mobile.technician.request.show', $id)->with('success', 'Work started.');
    }

    public function finishRequest($id)
    {
        $user = Auth::user();
        $request = \App\Models\MaintenanceRequest::where('id', $id)->where('assigned_to', $user->id)->firstOrFail();
        $request->status = 'completed';
        $request->completed_at = now();
        $request->save();
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
}
