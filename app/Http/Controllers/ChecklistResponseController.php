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
    public function store(Request $request, MaintenanceRequest $maintenance, $checklistItem)
    {
        try {
            // Handle both model binding and manual lookup
            if (!($checklistItem instanceof ChecklistItem)) {
                $checklistItem = ChecklistItem::findOrFail($checklistItem);
            }
            
            // Log the incoming request for debugging
            \Log::info('Checklist response store called', [
                'request_id' => $maintenance->id,
                'item_id' => $checklistItem->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'url' => $request->url(),
                'method' => $request->method()
            ]);
            
            $this->authorize('update', $maintenance);

            \Log::info('About to validate request');
            
            $request->validate([
                'is_completed' => 'required',
                'text_response' => 'nullable|string|max:1000',
                'response_attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048',
            ]);
            
            \Log::info('Validation passed');

        $response_attachment_path = null;
        
        if ($request->hasFile('response_attachment')) {
            $file = $request->file('response_attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('checklist-responses', $filename, 'public');
            $response_attachment_path = $path;
        }

        \Log::info('About to perform database operation');
        
        // Update or create response
        $response = ChecklistResponse::updateOrCreate(
            [
                'maintenance_request_id' => $maintenance->id,
                'checklist_item_id' => $checklistItem->id,
            ],
            [
                'is_completed' => $request->boolean('is_completed'),
                'text_response' => $request->text_response,
                'response_attachment_path' => $response_attachment_path,
            ]
        );
        
        \Log::info('Database operation completed successfully');

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist item response saved successfully.',
                'response' => $response
            ]);
        }

        return redirect()->back()->with('success', 'Checklist item response saved successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Checklist response error: ' . $e->getMessage(), [
                'request_id' => $maintenance->id ?? 'unknown',
                'item_id' => $checklistItem->id ?? 'unknown',
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'url' => $request->url(),
                'method' => $request->method()
            ]);

            // Check if it's a database connection error
            if (str_contains($e->getMessage(), 'No connection could be made') || 
                str_contains($e->getMessage(), 'SQLSTATE[HY000] [2002]')) {
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Database connection error. Please try again later.'
                    ], 503);
                }
                
                return redirect()->back()->with('error', 'Database connection error. Please try again later.');
            }

            // Return JSON error response for AJAX requests
            if ($request->expectsJson()) {
                $statusCode = 500;
                
                // Check for specific error types
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $statusCode = 404;
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    $statusCode = 403;
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422;
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating checklist item: ' . $e->getMessage(),
                    'error_type' => get_class($e)
                ], $statusCode);
            }

            return redirect()->back()->with('error', 'Error updating checklist item: ' . $e->getMessage());
        }
    }

    /**
     * Update a checklist response.
     */
    public function update(Request $request, MaintenanceRequest $maintenance, ChecklistResponse $response)
    {
        $this->authorize('update', $maintenance);

        $request->validate([
            'is_completed' => 'required',
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
    public function destroy(MaintenanceRequest $maintenance, ChecklistResponse $response)
    {
        $this->authorize('update', $maintenance);

        // Delete attachment if exists
        if ($response->response_attachment_path && Storage::disk('public')->exists($response->response_attachment_path)) {
            Storage::disk('public')->delete($response->response_attachment_path);
        }

        $response->delete();

        return redirect()->back()->with('success', 'Checklist item response deleted successfully.');
    }
}
