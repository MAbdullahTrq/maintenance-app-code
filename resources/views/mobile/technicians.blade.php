@extends('mobile.layout')

@section('title', 'Technicians')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div x-data="{ showForm: false, search: '', dropdownOpen: false, dropdownTop: 0, dropdownLeft: 0, dropdownTech: null, dropdownTechActive: null, showDeactivateConfirm: false, deactivateForm: null }" x-init="dropdownTechActive = null">
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg md:text-xl lg:text-2xl">All Technicians</div>
                
                <!-- Add Technician Button -->
                @if(Auth::user()->hasActiveSubscription() && !Auth::user()->isViewer())
                <a href="{{ route('mobile.technicians.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>Add
                </a>
                @elseif(Auth::user()->isViewer())
                    <!-- Viewers see no add button -->
                @else
                <a href="{{ route('mobile.subscription.plans') }}" class="bg-gray-400 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium" title="Subscription required">
                    <i class="fas fa-lock mr-1"></i>Add
                </a>
                @endif
            </div>
            <input type="text" x-model="search" placeholder="Search" class="w-full border rounded p-2 md:p-3 lg:p-4 mb-4 text-sm md:text-base" />
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Photo</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Name</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Contact</th>
                            <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicians as $tech)
                        <tr x-ref="'row_{{ $tech->id }}'" data-active="{{ $tech->is_active ? '1' : '0' }}" class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer @if(!$tech->is_active) opacity-60 bg-gray-100 @endif" x-show="!search || '{{ str_replace(["\r", "\n", "'"], ['', '', "\\'"], strtolower($tech->name)) }}'.includes(search.toLowerCase()) || '{{ str_replace(["\r", "\n", "'"], ['', '', "\\'"], strtolower($tech->email)) }}'.includes(search.toLowerCase()) || '{{ str_replace(["\r", "\n", "'"], ['', '', "\\'"], strtolower($tech->phone)) }}'.includes(search.toLowerCase())" onclick="window.location.href='{{ route('mobile.technicians.show', $tech->id) }}'">
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                                @if($tech->image)
                                    <img src="{{ asset('storage/' . $tech->image) }}" class="rounded-full w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 object-cover mx-auto" alt="Profile">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($tech->name) }}&background=eee&color=555&size=48" class="rounded-full w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 mx-auto" alt="Profile">
                                @endif
                                @if(!$tech->is_active)
                                    <span class="block text-xs text-red-600 font-bold mt-1">Deactivated</span>
                                @endif
                            </td>
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 font-semibold">{{ $tech->name }}</td>
                            <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                <a href="mailto:{{ $tech->email }}" class="text-blue-700 underline hover:text-blue-900 block">{{ $tech->email }}</a>
                                <a href="tel:{{ $tech->phone }}" class="text-gray-700 hover:text-gray-900">{{ $tech->phone }}</a>
                            </td>
                            <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                                <div class="relative">
                                    <button @click.prevent.stop="
                                        dropdownOpen = true;
                                        dropdownTech = {{ $tech->id }};
                                        dropdownTechActive = {{ $tech->is_active ? 'true' : 'false' }};
                                        const rect = $event.target.getBoundingClientRect();
                                        const menuHeight = 200; // Approximate height of dropdown
                                        const windowHeight = window.innerHeight;
                                        const spaceBelow = windowHeight - rect.bottom;
                                        const spaceAbove = rect.top;
                                        
                                        // Calculate vertical position
                                        if (spaceBelow >= menuHeight || spaceBelow > spaceAbove) {
                                            // Open downwards
                                            dropdownTop = rect.bottom + window.scrollY + 2;
                                        } else {
                                            // Open upwards
                                            dropdownTop = rect.top + window.scrollY - menuHeight - 2;
                                        }
                                        
                                        // Calculate horizontal position
                                        let left = rect.left + window.scrollX;
                                        const menuWidth = 150;
                                        if (left + menuWidth > window.innerWidth) {
                                            left = window.innerWidth - menuWidth - 8;
                                        }
                                        dropdownLeft = left;
                                    " class="px-2 py-1 text-gray-600 hover:text-gray-800 text-lg md:text-xl"><i class="fas fa-ellipsis-h"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Single dropdown menu rendered outside the table -->
            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="fixed z-[9999] bg-white rounded shadow-lg border text-xs min-w-max" x-cloak :style="'top:'+dropdownTop+'px;left:'+dropdownLeft+'px;'">
                <template x-if="dropdownTech">
                    <div>
                        @if(!Auth::user()->isViewer())
                        <a :href="'{{ url('m/at') }}/' + dropdownTech + '/edit'" class="block px-4 py-2 hover:bg-gray-100">Edit</a>
                        @endif
                        <a :href="'{{ url('m/at') }}/' + dropdownTech" class="block px-4 py-2 hover:bg-gray-100">View</a>
                        @if(!Auth::user()->isViewer())
                        <template x-if="dropdownTechActive">
                            <form :action="'{{ url('m/at') }}/' + dropdownTech + '/deactivate'" method="POST" class="block" @submit.prevent="showDeactivateConfirm = true; deactivateForm = $event.target; dropdownOpen = false;">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Deactivate</button>
                            </form>
                        </template>
                        <template x-if="!dropdownTechActive">
                            <form :action="'{{ url('m/at') }}/' + dropdownTech + '/activate'" method="POST" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Activate</button>
                            </form>
                        </template>
                        <form :action="'{{ url('m/technicians') }}/' + dropdownTech + '/reset-password'" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Reset Password</button>
                        </form>
                        @endif
                    </div>
                </template>
            </div>
            <!-- Confirmation Modal -->
            <div x-show="showDeactivateConfirm" class="fixed inset-0 bg-gray-900 bg-opacity-30 flex items-center justify-center z-[99999]">
                <div class="bg-white rounded shadow-lg p-6 max-w-sm w-full">
                    <div class="font-bold text-lg mb-2">Deactivate Technician?</div>
                    <div class="mb-4 text-gray-700">Are you sure you want to deactivate this technician? They will no longer be able to access the system.</div>
                    <div class="flex justify-end gap-2">
                        <button @click="showDeactivateConfirm = false" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                        <button @click="deactivateForm.submit(); showDeactivateConfirm = false;" class="px-4 py-2 bg-red-600 text-white rounded">Deactivate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 