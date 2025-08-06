@extends('mobile.layout')
@section('title', 'Add Technician')
@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <h2 class="text-center text-lg font-bold mb-4">Add Technician</h2>
        
        <!-- Usage Information -->
        @php
            $user = Auth::user();
            $limits = $user->getSubscriptionLimits();
            $currentCount = $user->getCurrentTechnicianCount();
            $remaining = $user->getRemainingTechnicianSlots();
            $planType = $user->isOnTrial() ? 'Trial' : 'Plan';
        @endphp
        <div class="mb-4 p-3 bg-gray-50 rounded-lg border">
            <div class="text-sm text-gray-600 mb-2">{{ $planType }} Usage:</div>
            <div class="flex justify-between items-center">
                <span class="text-sm">Technicians:</span>
                <span class="text-sm font-medium {{ $remaining <= 0 ? 'text-red-600' : ($remaining <= 2 ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ $currentCount }} / {{ $limits['technician_limit'] }}
                    @if($remaining > 0)
                        <span class="text-gray-500">({{ $remaining }} left)</span>
                    @endif
                </span>
            </div>
            @if($remaining <= 0)
                <div class="mt-2 text-xs text-red-600">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    You've reached your {{ strtolower($planType) }} limit. <a href="{{ route('mobile.subscription.plans') }}" class="underline">Upgrade to add more technicians.</a>
                </div>
            @endif
        </div>
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('mobile.technicians.store') }}" enctype="multipart/form-data" id="add-technician-form">
            @csrf
            <div class="mb-3">
                <label class="block font-semibold mb-1">Name*</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Email*</label>
                <input type="email" name="email" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Phone*</label>
                <input type="text" name="phone" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-semibold mb-1">Profile Picture</label>
                <input type="file" name="image" id="technician-image-input" class="w-full border rounded p-2" accept="image/*">
                <img id="image-preview" src="#" alt="Preview" class="mt-2 rounded max-h-32 hidden" />
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Add Technician</button>
        </form>
    </div>
</div>
<script>
// Image resize before upload
const input = document.getElementById('technician-image-input');
const form = document.getElementById('add-technician-form');
const preview = document.getElementById('image-preview');
input.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        const img = new Image();
        img.onload = function() {
            const MAX_SIZE = 600;
            let width = img.width;
            let height = img.height;
            if (width > height) {
                if (width > MAX_SIZE) {
                    height *= MAX_SIZE / width;
                    width = MAX_SIZE;
                }
            } else {
                if (height > MAX_SIZE) {
                    width *= MAX_SIZE / height;
                    height = MAX_SIZE;
                }
            }
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            canvas.toBlob(function(blob) {
                // Replace the file in the input
                const resizedFile = new File([blob], file.name, {type: blob.type});
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(resizedFile);
                input.files = dataTransfer.files;
            }, file.type, 0.85);
            // Show preview
            preview.src = canvas.toDataURL(file.type, 0.85);
            preview.classList.remove('hidden');
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
@endsection 