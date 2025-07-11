@extends('mobile.layout')

@section('title', 'Maintenance Request')

@section('header-actions')
<a href="#" class="text-sm font-medium">Manager &gt;</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-3 md:p-4 lg:p-6 w-full max-w-6xl mx-auto">
        <div class="mb-2 flex items-center">
            <a href="#" onclick="window.history.back(); return false;" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </div>
        <div class="mb-4 md:mb-6 text-center">
            <span class="inline-block bg-gray-200 px-2 md:px-3 py-1 md:py-2 rounded text-xs md:text-sm font-semibold mb-1 md:mb-2">{{ ucfirst($request->status) }}</span>
            <div class="font-bold text-lg md:text-xl lg:text-2xl">Maintenance Request</div>
            <div class="text-xs md:text-sm text-gray-500">({{ $request->property->name }})</div>
            <div class="text-xs md:text-sm text-gray-500">{{ \Carbon\Carbon::parse($request->created_at)->format('d M, Y H:i') }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="grid grid-cols-2 border border-gray-400 rounded mb-4 md:mb-6 text-center">
            <div class="py-2 md:py-3 lg:py-4 border-r border-gray-400 text-sm md:text-base lg:text-lg font-semibold {{ $request->priority == 'high' ? 'bg-red-500 text-white' : ($request->priority == 'low' ? 'bg-yellow-200' : ($request->priority == 'medium' ? 'bg-yellow-100' : '')) }}">
                {{ ucfirst($request->priority) }}
            </div>
            <div class="py-2 md:py-3 lg:py-4">
                <div class="text-xs md:text-sm font-semibold">Started:</div>
                <div class="text-xs md:text-sm lg:text-base">{{ $request->started_at ? \Carbon\Carbon::parse($request->started_at)->format('d M, Y H:i') : '-' }}</div>
                <div class="text-xs md:text-sm font-semibold mt-1">Finished:</div>
                <div class="text-xs md:text-sm lg:text-base">{{ $request->completed_at ? \Carbon\Carbon::parse($request->completed_at)->format('d M, Y H:i') : '-' }}</div>
            </div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4 md:mb-6">
            <div class="font-semibold text-sm md:text-base">Property name</div>
            <div class="text-sm md:text-base lg:text-lg">{{ $request->property->name }}</div>
            <div class="font-semibold mt-2 md:mt-3 text-sm md:text-base">Property address</div>
            <div class="text-sm md:text-base lg:text-lg">{{ $request->property->address }}</div>
            @if($request->property->special_instructions)
                <div class="font-semibold mt-2 md:mt-3 text-sm md:text-base">Special instructions</div>
                <div class="text-sm md:text-base lg:text-lg">{{ $request->property->special_instructions }}</div>
            @endif
        </div>
        <hr class="my-4 border-gray-300">
        @if($request->assignedTechnician)
        <div class="mb-4">
            <div class="font-semibold">Assigned Technician</div>
            <div>{{ $request->assignedTechnician->name }}</div>
            <div class="text-xs text-gray-500">{{ $request->assignedTechnician->email }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        @endif
        <div class="mb-4">
            <div class="font-semibold">Request title</div>
            <div>{{ $request->title }}</div>
            <div class="font-semibold mt-2">Description</div>
            <div>{{ $request->description }}</div>
            <div class="font-semibold mt-2">Location</div>
            <div>{{ $request->location }}</div>
        </div>
        <hr class="my-4 border-gray-300">
        <div class="mb-4 md:mb-6">
            <div class="font-bold text-lg md:text-xl lg:text-2xl mb-2 md:mb-3">Requester Info</div>
            <div class="mb-2"><span class="font-semibold text-sm md:text-base">Requester name:</span> <span class="text-sm md:text-base lg:text-lg">{{ $request->requester_name ?? 'Not provided' }}</span></div>
            <div class="mb-2"><span class="font-semibold text-sm md:text-base">Email:</span> <span class="text-sm md:text-base lg:text-lg">{{ $request->requester_email ?? 'Not provided' }}</span></div>
            <div class="mb-1"><span class="font-semibold text-sm md:text-base">Phone:</span> <span class="text-sm md:text-base lg:text-lg">{{ $request->requester_phone ?? 'Not provided' }}</span></div>
        </div>
        <hr class="my-4 border-gray-300">
        <!-- Images Section with Popup -->
        <div x-data="{ showModal: false, modalImage: '' }" class="mb-4 md:mb-6">
            <div class="flex flex-wrap gap-2 md:gap-3 mb-2">
                @foreach($request->images as $image)
                    <img 
                        src="{{ asset('storage/' . $image->image_path) }}" 
                        alt="Request Image" 
                        class="w-16 h-16 md:w-20 md:h-20 lg:w-24 lg:h-24 object-cover rounded cursor-pointer hover:opacity-80 transition-opacity"
                        @click="showModal = true; modalImage = '{{ asset('storage/' . $image->image_path) }}'"
                    >
                @endforeach
            </div>
            <!-- Modal -->
            <div 
                x-show="showModal" 
                x-transition 
                class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
                @click.self="showModal = false"
                style="display: none;"
            >
                <img :src="modalImage" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg border-4 border-white">
            </div>
        </div>
        <hr class="my-4 border-gray-300">
        @if($request->comments && $request->comments->count() > 0)
        <div class="mb-4">
            <div class="font-semibold mb-1">Comments</div>
            <div class="space-y-2">
                @foreach($request->comments as $comment)
                    @php $isOwn = auth()->check() && $comment->user_id === auth()->id(); @endphp
                    <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                        <div class="rounded p-2 max-w-[75%] {{ $isOwn ? 'bg-blue-500 text-white text-right' : 'bg-gray-100 text-gray-900 text-left' }}">
                            <div class="text-xs {{ $isOwn ? 'text-blue-100' : 'text-gray-600' }} mb-1">
                                {{ $comment->user->name ?? 'User' }} &middot; {{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}
                            </div>
                            <div class="text-sm">{{ $comment->comment }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        <hr class="my-4 border-gray-300">
        <form method="POST" action="{{ route('mobile.request.comment', $request->id) }}" class="mb-4">
            @csrf
            <textarea name="comment" class="w-full border rounded p-2 mb-2" placeholder="Add a comment..." required></textarea>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Add Comment</button>
        </form>
        <hr class="my-4 border-gray-300">
        {{-- ACTIONS SECTION --}}
        <div class="mb-2">
            @if($request->status === 'pending' && auth()->user() && auth()->user()->isPropertyManager())
                <form method="POST" action="{{ route('mobile.request.approve', $request->id) }}" class="mb-2 flex flex-col gap-2" x-data="{ tech: '' }">
                    @csrf
                    <div class="mb-2">
                        <label class="block font-semibold mb-1">Assign Technician*</label>
                        <select name="technician_id" class="w-full border rounded p-2" x-model="tech">
                            <option value="">Select Technician</option>
                            @foreach(App\Models\User::whereHas('role', function($q){$q->where('slug','technician');})->get() as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="w-1/2 bg-green-500 text-white py-2 rounded" :disabled="!tech">Assign & Accept</button>
                        <button type="button" onclick="document.getElementById('declineModal').classList.remove('hidden')" class="w-1/2 bg-gray-300 text-black py-2 rounded">Decline</button>
                    </div>
                </form>
                <div id="declineModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                    <form method="POST" action="{{ route('mobile.request.decline', $request->id) }}" class="bg-white p-4 rounded shadow max-w-xs w-full">
                        @csrf
                        <div class="mb-2 font-semibold">Reason for Decline</div>
                        <textarea name="comment" class="w-full border rounded p-2 mb-2" required></textarea>
                        <div class="flex gap-2">
                            <button type="submit" class="w-1/2 bg-red-500 text-white py-2 rounded">Decline</button>
                            <button type="button" onclick="document.getElementById('declineModal').classList.add('hidden')" class="w-1/2 bg-gray-300 text-black py-2 rounded">Cancel</button>
                        </div>
                    </form>
                </div>
            @elseif($request->status === 'assigned' && auth()->user() && auth()->user()->isTechnician() && $request->assigned_to == auth()->id())
                <form method="POST" action="{{ route('mobile.request.accept', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Accept</button>
                </form>
            @elseif($request->status === 'accepted' && auth()->user() && auth()->user()->isTechnician() && $request->assigned_to == auth()->id())
                <form method="POST" action="{{ route('mobile.request.start', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Start</button>
                </form>
            @elseif($request->status === 'started' && auth()->user() && auth()->user()->isTechnician() && $request->assigned_to == auth()->id())
                <form method="POST" action="{{ route('mobile.request.finish', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Finish</button>
                </form>

            @endif
            {{-- Always show Mark as Complete if eligible --}}
            @if(auth()->user() && auth()->user()->isPropertyManager() && in_array($request->status, ['pending', 'assigned', 'started', 'acknowledged', 'accepted']))
            <form method="POST" action="{{ route('mobile.request.complete', $request->id) }}" class="mb-2">
                @csrf
                <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Mark as Complete</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection 