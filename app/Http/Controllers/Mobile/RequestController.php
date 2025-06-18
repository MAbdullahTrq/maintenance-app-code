<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Mail\TechnicianAssignedNotification;
use App\Mail\TechnicianStartedNotification;
use App\Mail\TechnicianCompletedNotification;
use App\Mail\TechnicianCommentNotification;
use App\Mail\ManagerCommentNotification;
use App\Mail\TechnicianStartedRequesterNotification;
use App\Mail\TechnicianCompletedRequesterNotification;
use Illuminate\Support\Facades\Mail;

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
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $technician = \App\Models\User::findOrFail($request->technician_id);
        $maintenance->assigned_to = $technician->id;
        $maintenance->status = 'assigned';
        $maintenance->approved_by = auth()->id();
        $maintenance->save();
        // Send notification to technician
        Mail::to($technician->email)->send(new TechnicianAssignedNotification($maintenance));
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request approved and assigned to ' . $technician->name . '.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request approved and technician assigned.');
    }

    public function decline(Request $request, $id)
    {
        $request->validate([
            'decline_reason' => 'required|string',
        ]);
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'declined';
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request declined: ' . $request->decline_reason,
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request declined.');
    }

    public function complete(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'completed';
        $maintenance->completed_at = now();
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request marked as completed by manager.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request marked as completed.');
    }

    public function close(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'closed';
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request closed by manager.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request closed.');
    }

    public function accept(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'accepted';
        $maintenance->save();
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request accepted by technician.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request accepted.');
    }

    public function start(Request $request, $id)
    {
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'started';
        $maintenance->started_at = now();
        $maintenance->save();
        // Send notifications
        Mail::to($maintenance->property->manager->email)
            ->send(new TechnicianStartedNotification($maintenance));

        if ($maintenance->requester_email) {
            Mail::to($maintenance->requester_email)
                ->send(new TechnicianStartedRequesterNotification($maintenance));
        }
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request started by technician.',
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request started.');
    }

    public function finish(Request $request, $id)
    {
        $request->validate([
            'completion_comment' => 'required|string',
        ]);
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $maintenance->status = 'completed';
        $maintenance->completed_at = now();
        $maintenance->save();
        // Send notifications
        Mail::to($maintenance->property->manager->email)
            ->send(new TechnicianCompletedNotification($maintenance));

        if ($maintenance->requester_email) {
            Mail::to($maintenance->requester_email)
                ->send(new TechnicianCompletedRequesterNotification($maintenance));
        }
        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => 'Request completed by technician: ' . $request->completion_comment,
        ]);

        return redirect()->route('mobile.request.show', $id)->with('success', 'Request completed.');
    }

    public function comment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);
        $maintenance = \App\Models\MaintenanceRequest::findOrFail($id);
        $comment = $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        // Send notifications based on who is commenting
        if (auth()->user()->isTechnician()) {
            // Technician commenting - notify manager only (removed requester notification)
            Mail::to($maintenance->property->manager->email)
                ->send(new TechnicianCommentNotification($maintenance, $comment));
        } elseif (auth()->user()->isPropertyManager() || auth()->user()->isAdmin()) {
            // Manager commenting - notify technician
            if ($maintenance->assignedTechnician) {
                Mail::to($maintenance->assignedTechnician->email)
                    ->send(new ManagerCommentNotification($maintenance, $comment));
            }
        }

        return redirect()->route('mobile.request.show', $id)->with('success', 'Comment added.');
    }
}
