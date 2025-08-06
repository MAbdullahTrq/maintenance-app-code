@extends('mobile.layout')

@section('title', 'Profile')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full mt-4">
        <h2 class="text-center text-2xl font-bold mb-4">My Profile</h2>
        <div class="flex flex-col items-center mb-6">
            <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center mb-3 overflow-hidden border-4 border-gray-100 shadow-lg">
                @if($user->image)
                    <img src="{{ asset('storage/' . $user->image) }}" class="w-24 h-24 rounded-full object-cover" alt="Profile">
                @else
                    <i class="fas fa-user text-5xl text-gray-400"></i>
                @endif
            </div>
            <div class="text-lg font-semibold mt-2">{{ $user->name }}</div>
            <div class="text-gray-600 text-sm">{{ $user->email }}</div>
            @if($user->phone)
                <div class="text-gray-600 text-sm">{{ $user->phone }}</div>
            @endif
            <div class="text-gray-500 text-xs mt-1">Role: {{ $user->role->name ?? 'N/A' }}</div>
        </div>
        
        <form method="POST" action="{{ route('mobile.profile.update-picture') }}" enctype="multipart/form-data" class="w-full mb-6">
            @csrf
            <div class="space-y-4">
                <label class="block text-center font-semibold text-gray-700 mb-3">Change Profile Picture</label>
                
                <!-- Camera Capture Button -->
                <button type="button" onclick="openCamera()" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 hover:bg-green-700 transition-colors">
                    <i class="fas fa-camera"></i>
                    Take Photo
                </button>
                
                <!-- File Upload Button -->
                <div class="relative">
                    <label for="profile-image-input" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 hover:bg-blue-700 transition-colors cursor-pointer">
                        <i class="fas fa-upload"></i>
                        Choose from Gallery
                    </label>
                    <input id="profile-image-input" type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="hidden" onchange="handleFileSelect(this)">
                </div>
                
                <!-- Selected File Display -->
                <div id="file-info" class="hidden bg-gray-50 p-3 rounded-lg border">
                    <div class="flex items-center justify-between">
                        <span id="file-name" class="text-sm text-gray-700 truncate"></span>
                        <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="file-size" class="text-xs text-gray-500 mt-1"></div>
                </div>
                
                <!-- Upload Button -->
                <button type="submit" id="upload-btn" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Picture
                </button>
            </div>
        </form>
        
        <!-- Subscription Information -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                Subscription Status
            </h3>
            
            @if($user->isOnTrial())
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-blue-800">Free Trial</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Active
                        </span>
                    </div>
                    <div class="text-sm text-blue-700">
                        <p class="mb-1">Started: {{ $user->trial_started_at ? $user->trial_started_at->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
                        @if($user->trial_expires_at)
                            <p class="font-medium">
                                @php
                                    $daysLeft = now()->diffInDays($user->trial_expires_at, false);
                                @endphp
                                @if($daysLeft > 0)
                                    Expires on {{ $user->trial_expires_at->format('M d, Y \a\t g:i A') }}
                                @elseif($daysLeft == 0)
                                    Expires today at {{ $user->trial_expires_at->format('g:i A') }}
                                @else
                                    Expired on {{ $user->trial_expires_at->format('M d, Y \a\t g:i A') }}
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('mobile.subscription.plans') }}" class="block w-full bg-green-600 text-white py-2 rounded-lg font-semibold text-center hover:bg-green-700 transition-colors text-sm">
                            <i class="fas fa-credit-card mr-2"></i>Upgrade to Paid Plan
                        </a>
                    </div>
                </div>
            @elseif($user->hasActiveSubscription())
                @php
                    $activeSubscription = $user->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->first();
                @endphp
                @if($activeSubscription)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-green-800">{{ $activeSubscription->plan->name ?? 'Active Plan' }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                        <div class="text-sm text-green-700">
                            <p class="mb-1">Started: {{ $activeSubscription->created_at->format('M d, Y \a\t g:i A') }}</p>
                            <p class="mb-1">Next billing: {{ $activeSubscription->ends_at->format('M d, Y \a\t g:i A') }}</p>
                            @if($activeSubscription->plan)
                                <p class="font-medium">€{{ number_format($activeSubscription->plan->price, 2) }}/month</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div class="text-sm text-gray-600">
                            <p>No active subscription found.</p>
                        </div>
                    </div>
                @endif
            @elseif($user->isInGracePeriod())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-yellow-800">Grace Period</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Expired
                        </span>
                    </div>
                                            <div class="text-sm text-yellow-700">
                            <p class="mb-1">Trial expired on: {{ $user->trial_expires_at ? $user->trial_expires_at->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
                            @php
                                $graceEndDate = $user->trial_expires_at ? $user->trial_expires_at->addDays(7) : null;
                                $graceDaysLeft = $graceEndDate ? now()->diffInDays($graceEndDate, false) : 0;
                            @endphp
                            @if($graceDaysLeft > 0)
                                <p class="font-medium">Grace period ends on {{ $graceEndDate->format('M d, Y \a\t g:i A') }}</p>
                            @else
                                <p class="font-medium">Grace period ended on {{ $graceEndDate ? $graceEndDate->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
                            @endif
                        </div>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-red-800">No Active Subscription</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Inactive
                        </span>
                    </div>
                    <div class="text-sm text-red-700">
                        <p>Your account is not active. Please subscribe to continue.</p>
                    </div>
                </div>
            @endif
            
            @if(!$user->hasActiveSubscription() && !$user->isOnTrial())
                <div class="mt-3">
                    <a href="{{ route('mobile.subscription.plans') }}" class="block w-full bg-blue-600 text-white py-2 rounded-lg font-semibold text-center hover:bg-blue-700 transition-colors text-sm">
                        <i class="fas fa-credit-card mr-2"></i>Subscribe Now
                    </a>
                </div>
            @endif
        </div>
        
        <div class="space-y-3">
            <a href="{{ route('mobile.profile.change-password') }}" class="block w-full bg-yellow-500 text-white py-3 rounded-lg font-semibold text-center hover:bg-yellow-600 transition-colors">
                <i class="fas fa-key mr-2"></i>Change Password
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-red-700 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div id="camera-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Take Photo</h3>
                    <button onclick="closeCamera()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <video id="camera-video" class="w-full h-64 bg-gray-900 rounded-lg" autoplay></video>
                <canvas id="camera-canvas" class="hidden"></canvas>
                <div class="flex gap-2 mt-4">
                    <button onclick="capturePhoto()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">
                        <i class="fas fa-camera mr-2"></i>Capture
                    </button>
                    <button onclick="closeCamera()" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let stream = null;
let capturedImage = null;

function openCamera() {
    const modal = document.getElementById('camera-modal');
    const video = document.getElementById('camera-video');
    
    modal.classList.remove('hidden');
    
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
        .then(function(mediaStream) {
            stream = mediaStream;
            video.srcObject = mediaStream;
        })
        .catch(function(err) {
            console.error('Camera access denied:', err);
            alert('Camera access is required to take a photo. Please allow camera access and try again.');
            closeCamera();
        });
}

function closeCamera() {
    const modal = document.getElementById('camera-modal');
    modal.classList.add('hidden');
    
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    
    const video = document.getElementById('camera-video');
    video.srcObject = null;
}

function capturePhoto() {
    const video = document.getElementById('camera-video');
    const canvas = document.getElementById('camera-canvas');
    const context = canvas.getContext('2d');
    
    // Set canvas size to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert canvas to blob
    canvas.toBlob(function(blob) {
        capturedImage = blob;
        
        // Create a file from the blob
        const file = new File([blob], 'camera-photo.jpg', { type: 'image/jpeg' });
        
        // Set the file input
        const fileInput = document.getElementById('profile-image-input');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;
        
        // Update UI
        handleFileSelect(fileInput);
        
        // Close camera
        closeCamera();
    }, 'image/jpeg', 0.8);
}

function handleFileSelect(input) {
    const file = input.files[0];
    if (file) {
        // Check file size (10MB = 10 * 1024 * 1024 bytes)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            alert('File is too large. Please select a file smaller than 10MB.');
            clearFile();
            return;
        }
        
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const uploadBtn = document.getElementById('upload-btn');
        
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.classList.remove('hidden');
        uploadBtn.disabled = false;
    }
}

function clearFile() {
    const fileInput = document.getElementById('profile-image-input');
    const fileInfo = document.getElementById('file-info');
    const uploadBtn = document.getElementById('upload-btn');
    
    fileInput.value = '';
    fileInfo.classList.add('hidden');
    uploadBtn.disabled = true;
    capturedImage = null;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endsection 