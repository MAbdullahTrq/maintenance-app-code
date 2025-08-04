<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\ChecklistResponse;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChecklistResponseController extends Controller
{
    /**
     * Store a checklist response.
     */
    public function store(Request $request, MaintenanceRequest $maintenanceRequest, ChecklistItem $item)
    {
        $this->authorize('update', $maintenanceRequest);

        $request->validate([
            'is_completed' => 'required|boolean',
            'text_response' => 'nullable|string|max:1000',
            'response_attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048',
        ]);

        $response_attachment_path = null;
        
        if ($request->hasFile('response_attachment')) {
            $file = $request->file('response_attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('checklist-responses', $filename, 'public');
            $response_attachment_path = $path;
        }

        // Update or create response
        $response = ChecklistResponse::updateOrCreate(
            [
                'maintenance_request_id' => $maintenanceRequest->id,
                'checklist_item_id' => $item->id,
            ],
            [
                'is_completed' => $request->boolean('is_completed'),
                'text_response' => $request->text_response,
                'response_attachment_path' => $response_attachment_path,
            ]
        );

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist item response saved successfully.',
                'response' => $response
            ]);
        }

        return redirect()->back()->with('success', 'Checklist item response saved successfully.');
    }

    /**
     * Update a checklist response.
     */
    public function update(Request $request, MaintenanceRequest $maintenanceRequest, ChecklistResponse $response)
    {
        $this->authorize('update', $maintenanceRequest);

        $request->validate([
            'is_completed' => 'required|boolean',
            'text_response' => 'nullable|string|max:1000',
            'response_attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048',
        ]);

        $response_attachment_path = $response->response_attachment_path;
        
        if ($request->hasFile('response_attachment')) {
            // Delete old attachment if exists
            if ($response->response_attachment_path && Storage::disk('public')->exists($response->response_attachment_path)) {
                Storage::disk('public')->delete($response->response_attachment_path);
            }
            
            $file = $request->file('response_attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('checklist-responses', $filename, 'public');
            $response_attachment_path = $path;
        }

        $response->update([
            'is_completed' => $request->boolean('is_completed'),
            'text_response' => $request->text_response,
            'response_attachment_path' => $response_attachment_path,
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist item response updated successfully.',
                'response' => $response
            ]);
        }

        return redirect()->back()->with('success', 'Checklist item response updated successfully.');
    }

    /**
     * Delete a checklist response.
     */
    public function destroy(MaintenanceRequest $maintenanceRequest, ChecklistResponse $response)
    {
        $this->authorize('update', $maintenanceRequest);

        // Delete attachment if exists
        if ($response->response_attachment_path && Storage::disk('public')->exists($response->response_attachment_path)) {
            Storage::disk('public')->delete($response->response_attachment_path);
        }

        $response->delete();

        return redirect()->back()->with('success', 'Checklist item response deleted successfully.');
    }
}
