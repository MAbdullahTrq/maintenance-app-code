<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChecklistItemController extends Controller
{
    protected $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Store a newly created checklist item.
     */
    public function store(Request $request, Checklist $checklist)
    {
        $this->authorize('update', $checklist);

        $request->validate([
            'type' => 'required|in:checkbox,header',
            'description' => 'required|string|max:500',
            'task_description' => 'nullable|string|max:1000',
            'is_required' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240', // 10MB max for images
        ]);

        $attachment_path = null;
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $mediaType = $file->getMimeType();
            $isImage = strpos($mediaType, 'image/') === 0;
            
            if ($isImage) {
                // Optimize images using the existing service
                $attachment_path = $this->imageService->optimizeAndResize(
                    $file,
                    'checklist_attachments',
                    800, // width
                    600  // height
                );
            } else {
                // Store non-image files directly
                $filename = time() . '_' . $file->getClientOriginalName();
                $attachment_path = $file->storeAs('checklist_attachments', $filename, 'public');
            }
        }

        $order = $checklist->items()->max('order') + 1;

        $checklist->items()->create([
            'type' => $request->type,
            'description' => $request->description,
            'task_description' => $request->task_description,
            'is_required' => $request->boolean('is_required'),
            'attachment_path' => $attachment_path,
            'order' => $order,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist item added successfully.',
                'item' => $checklist->items()->latest()->first()
            ]);
        }
        
        return redirect()->route('mobile.checklists.edit', $checklist)
            ->with('success', 'Checklist item added successfully.');
    }

    /**
     * Update the specified checklist item.
     */
    public function update(Request $request, Checklist $checklist, ChecklistItem $item)
    {
        $this->authorize('update', $checklist);

        $request->validate([
            'type' => 'required|in:checkbox,header',
            'description' => 'required|string|max:500',
            'task_description' => 'nullable|string|max:1000',
            'is_required' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240', // 10MB max for images
        ]);

        $attachment_path = $item->attachment_path;
        
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($item->attachment_path && Storage::disk('public')->exists($item->attachment_path)) {
                Storage::disk('public')->delete($item->attachment_path);
            }
            
            $file = $request->file('attachment');
            $mediaType = $file->getMimeType();
            $isImage = strpos($mediaType, 'image/') === 0;
            
            if ($isImage) {
                // Optimize images using the existing service
                $attachment_path = $this->imageService->optimizeAndResize(
                    $file,
                    'checklist_attachments',
                    800, // width
                    600  // height
                );
            } else {
                // Store non-image files directly
                $filename = time() . '_' . $file->getClientOriginalName();
                $attachment_path = $file->storeAs('checklist_attachments', $filename, 'public');
            }
        }

        $item->update([
            'type' => $request->type,
            'description' => $request->description,
            'task_description' => $request->task_description,
            'is_required' => $request->boolean('is_required'),
            'attachment_path' => $attachment_path,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist item updated successfully.',
                'item' => $item->fresh()
            ]);
        }
        
        return redirect()->route('mobile.checklists.edit', $checklist)
            ->with('success', 'Checklist item updated successfully.');
    }

    /**
     * Remove the specified checklist item.
     */
    public function destroy(Checklist $checklist, $itemId)
    {
        $this->authorize('update', $checklist);

        // Find the item, but don't fail if it doesn't exist
        $item = ChecklistItem::where('id', $itemId)
                            ->where('checklist_id', $checklist->id)
                            ->first();

        if (!$item) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checklist item not found or already deleted.'
                ], 404);
            }
            
            return redirect()->route('mobile.checklists.edit', $checklist)
                ->with('error', 'Checklist item not found.');
        }

        // Delete attachment if exists
        if ($item->attachment_path && Storage::disk('public')->exists($item->attachment_path)) {
            Storage::disk('public')->delete($item->attachment_path);
        }

        $item->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist item deleted successfully.'
            ]);
        }
        
        return redirect()->route('mobile.checklists.edit', $checklist)
            ->with('success', 'Checklist item deleted successfully.');
    }

    /**
     * Update the order of checklist items.
     */
    public function updateOrder(Request $request, Checklist $checklist)
    {
        $this->authorize('update', $checklist);

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:checklist_items,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $itemData) {
            $checklist->items()
                ->where('id', $itemData['id'])
                ->update(['order' => $itemData['order']]);
        }

        return response()->json(['success' => true]);
    }
} 