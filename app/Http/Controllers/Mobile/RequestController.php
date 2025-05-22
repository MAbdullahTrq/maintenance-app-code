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
        return view('mobile.request', ['request' => $request]);
    }

    public function approve(Request $request, $id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'assigned';
        $maintenance->assigned_to = $request->input('technician_id');
        $maintenance->save();
        return redirect()->route('mobile.request.show', $id)->with('success', 'Request approved and technician assigned.');
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
        $maintenance->status = 'started';
        $maintenance->started_at = now();
        $maintenance->save();
        return redirect()->route('mobile.request.show', $id)->with('success', 'Work started.');
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
}
