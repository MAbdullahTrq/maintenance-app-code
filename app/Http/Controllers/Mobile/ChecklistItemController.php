<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChecklistItemController extends Controller
{
    /**
     * Store a newly created checklist item.
     */
    public function store(Request $request, Checklist $checklist)
    {
        $this->authorize('update', $checklist);

        $request->validate([
            'type' => 'required|in:text,checkbox',
            'description' => 'required|string|max:500',
            'is_required' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048',
        ]);

        $attachment_path = null;
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('checklist-attachments', $filename, 'public');
            $attachment_path = $path;
        }

        $order = $checklist->items()->max('order') + 1;

        $checklist->items()->create([
            'type' => $request->type,
            'description' => $request->description,
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
            'type' => 'required|in:text,checkbox',
            'description' => 'required|string|max:500',
            'is_required' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048',
        ]);

        $attachment_path = $item->attachment_path;
        
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($item->attachment_path && Storage::disk('public')->exists($item->attachment_path)) {
                Storage::disk('public')->delete($item->attachment_path);
            }
            
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('checklist-attachments', $filename, 'public');
            $attachment_path = $path;
        }

        $item->update([
            'type' => $request->type,
            'description' => $request->description,
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
    public function destroy(Checklist $checklist, ChecklistItem $item)
    {
        $this->authorize('update', $checklist);

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