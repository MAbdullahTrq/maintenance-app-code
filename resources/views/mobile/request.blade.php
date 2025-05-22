@extends('mobile.layout')

@section('title', 'Maintenance Request')

@section('header-actions')
<a href="#" class="text-sm font-medium">Manager &gt;</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="font-extrabold text-xl mb-2"><span class="text-blue-700">Maintain</span><span class="text-black">Xtra</span></div>
        <div class="mb-2 text-center">
            <span class="inline-block bg-gray-200 px-2 py-1 rounded text-xs font-semibold mb-1">{{ ucfirst($request->status) }}</span>
            <div class="font-bold text-lg">Maintenance Request</div>
            <div class="text-xs text-gray-500">({{ $request->property->name }})</div>
            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($request->created_at)->format('d M, Y H:i') }}</div>
        </div>
        <div class="grid grid-cols-2 border border-gray-400 rounded mb-4 text-center">
            <div class="py-2 border-r border-gray-400 {{ $request->priority == 'high' ? 'bg-red-500 text-white' : ($request->priority == 'low' ? 'bg-yellow-200' : ($request->priority == 'medium' ? 'bg-yellow-100' : '')) }}">
                {{ ucfirst($request->priority) }}
            </div>
            <div class="py-2">
                <div class="text-xs">Started:</div>
                <div class="text-sm">{{ $request->started_at ? \Carbon\Carbon::parse($request->started_at)->format('d M, Y H:i') : '-' }}</div>
                <div class="text-xs">Finished:</div>
                <div class="text-sm">{{ $request->completed_at ? \Carbon\Carbon::parse($request->completed_at)->format('d M, Y H:i') : '-' }}</div>
            </div>
        </div>
        <div class="mb-2">
            <div class="font-semibold">Property name</div>
            <div>{{ $request->property->name }}</div>
            <div class="font-semibold mt-2">Property address</div>
            <div>{{ $request->property->address }}</div>
            @if($request->property->special_instructions)
                <div class="font-semibold mt-2">Special instructions</div>
                <div>{{ $request->property->special_instructions }}</div>
            @endif
        </div>
        @if($request->assignedTechnician)
        <div class="mb-2">
            <div class="font-semibold">Assigned Technician</div>
            <div>{{ $request->assignedTechnician->name }}</div>
            <div class="text-xs text-gray-500">{{ $request->assignedTechnician->email }}</div>
        </div>
        @endif
        <div class="mb-2">
            <div class="font-semibold">Request title</div>
            <div>{{ $request->title }}</div>
            <div class="font-semibold mt-2">Description</div>
            <div>{{ $request->description }}</div>
            <div class="font-semibold mt-2">Location</div>
            <div>{{ $request->location }}</div>
        </div>
        <div class="mb-4"></div>
        {{-- ACTIONS SECTION --}}
        <div class="mb-2">
            @if($request->status === 'pending')
                <form method="POST" action="{{ route('maintenance.approve', $request->id) }}" class="mb-2 flex flex-col gap-2">
                    @csrf
                    <div class="mb-2">
                        <label class="block font-semibold mb-1">Assign Technician*</label>
                        <select name="technician_id" class="w-full border rounded p-2">
                            <option value="">Select Technician</option>
                            @foreach(App\Models\User::whereHas('role', function($q){$q->where('slug','technician');})->get() as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="w-1/2 bg-green-500 text-white py-2 rounded">Approve</button>
                        <button type="button" onclick="document.getElementById('declineModal').classList.remove('hidden')" class="w-1/2 bg-gray-300 text-black py-2 rounded">Decline</button>
                    </div>
                </form>
                <div id="declineModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                    <form method="POST" action="{{ route('maintenance.decline', $request->id) }}" class="bg-white p-4 rounded shadow max-w-xs w-full">
                        @csrf
                        <div class="mb-2 font-semibold">Reason for Decline</div>
                        <textarea name="comment" class="w-full border rounded p-2 mb-2" required></textarea>
                        <div class="flex gap-2">
                            <button type="submit" class="w-1/2 bg-red-500 text-white py-2 rounded">Decline</button>
                            <button type="button" onclick="document.getElementById('declineModal').classList.add('hidden')" class="w-1/2 bg-gray-300 text-black py-2 rounded">Cancel</button>
                        </div>
                    </form>
                </div>
            @elseif($request->status === 'assigned')
                <form method="POST" action="{{ route('maintenance.start-task', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Start</button>
                </form>
            @elseif($request->status === 'started')
                <form method="POST" action="{{ route('maintenance.finish-task', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Finish</button>
                </form>
            @elseif($request->status === 'completed')
                <form method="POST" action="{{ route('maintenance.close', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-gray-500 text-white py-2 rounded">Close Request</button>
                </form>
            @endif
            @if(in_array($request->status, ['assigned', 'started']))
                <form method="POST" action="{{ route('maintenance.complete', $request->id) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded">Mark as Complete</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection 