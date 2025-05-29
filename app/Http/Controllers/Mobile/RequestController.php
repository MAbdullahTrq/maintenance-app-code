<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;

class RequestController extends Controller
{
    public function show($id)
    {
        $request = MaintenanceRequest::with(['property', 'assignedTechnician', 'images'])->findOrFail($id);
        $user = auth()->user();
        $propertiesCount = \App\Models\Property::where('manager_id', $user->id)->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $user->id)->pluck('id'))->count();
        return view('mobile.request', [
            'request' => $request,
            'propertiesCount' => $propertiesCount,
            'techniciansCount' => $techniciansCount,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'accepted';
        $maintenance->assigned_to = $request->input('technician_id');
        $maintenance->save();
        return redirect()->route('mobile.request.show', $id)->with('success', 'Request assigned and accepted.');
    }

    public function accept($id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        if ($maintenance->status === 'assigned' && $maintenance->assigned_to == auth()->id()) {
            $maintenance->status = 'accepted';
            $maintenance->save();
        }
        return redirect()->route('mobile.request.show', $id)->with('success', 'Request accepted.');
    }

    public function decline(Request $request, $id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'declined';
        $maintenance->save();
        // Optionally, save the comment as a note or in a comments table
        return redirect()->route('mobile.request.show', $id)->with('success', 'Request declined.');
    }

    public function start($id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        if ($maintenance->status === 'accepted' && $maintenance->assigned_to == auth()->id()) {
            $maintenance->status = 'started';
            $maintenance->started_at = now();
            $maintenance->save();
            return redirect()->route('mobile.request.show', $id)->with('success', 'Work started.');
        }
        return redirect()->route('mobile.request.show', $id)->with('error', 'Cannot start this request.');
    }

    public function finish($id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'completed';
        $maintenance->completed_at = now();
        $maintenance->save();
        return redirect()->route('mobile.request.show', $id)->with('success', 'Work finished.');
    }

    public function complete($id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'completed';
        $maintenance->completed_at = now();
        $maintenance->save();
        return redirect()->route('mobile.request.show', $id)->with('success', 'Request marked as complete.');
    }

    public function close($id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'closed';
        $maintenance->save();
        return redirect()->route('mobile.request.show', $id)->with('success', 'Request closed.');
    }

    public function assignTechnician(Request $request, $id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);
        $maintenance->assigned_to = $request->input('technician_id');
        if ($maintenance->status === 'accepted') {
            $maintenance->status = 'assigned';
        }
        $maintenance->save();
        return redirect()->route('mobile.request.show', $id)->with('success', 'Technician assigned successfully.');
    }

    public function create()
    {
        $user = auth()->user();
        $properties = \App\Models\Property::where('manager_id', $user->id)->get();
        $propertiesCount = $properties->count();
        $techniciansCount = \App\Models\User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', $properties->pluck('id'))->count();
        return view('mobile.request_create', compact('properties', 'propertiesCount', 'techniciansCount', 'requestsCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'priority' => 'required',
        ]);
        $req = new \App\Models\MaintenanceRequest();
        $req->property_id = $request->property_id;
        $req->title = $request->title;
        $req->description = $request->description;
        $req->location = $request->location;
        $req->priority = $request->priority;
        $req->status = 'pending';
        $req->save();
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('requests', 'public');
                $req->images()->create(['image_path' => $path]);
            }
        }
        return redirect()->route('mobile.manager.all-requests')->with('success', 'Request submitted!');
    }
}
