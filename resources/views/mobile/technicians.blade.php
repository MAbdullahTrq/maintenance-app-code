@extends('mobile.layout')

@section('title', 'Technicians')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div x-data="{ showForm: false, search: '', dropdownOpen: false, dropdownTop: 0, dropdownLeft: 0, dropdownTech: null }">
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg">All Technicians</div>
                <button @click="showForm = !showForm" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Add Technician</button>
            </div>
            <input type="text" x-model="search" placeholder="Search" class="w-full border rounded p-2 mb-4" />
            <form x-show="showForm" method="POST" action="{{ route('mobile.technicians.store') }}" class="mb-4 bg-gray-50 p-3 rounded border" @submit="showForm = false">
                @csrf
                <input type="text" name="name" class="w-full border rounded p-2 mb-2" placeholder="Name" required>
                <input type="email" name="email" class="w-full border rounded p-2 mb-2" placeholder="Email" required>
                <input type="text" name="phone" class="w-full border rounded p-2 mb-2" placeholder="Phone" required>
                <div class="flex gap-2">
                    <button type="submit" class="w-1/2 bg-blue-700 text-white py-2 rounded">Add</button>
                    <button type="button" @click="showForm = false" class="w-1/2 bg-gray-300 text-black py-2 rounded">Cancel</button>
                </div>
            </form>
            <div class="overflow-x-visible">
                <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-1 border-r border-gray-400"> </th>
                            <th class="p-1 border-r border-gray-400">Name</th>
                            <th class="p-1 border-r border-gray-400">Address</th>
                            <th class="p-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicians as $tech)
                        <tr class="border-b border-gray-400" x-show="!search || '{{ strtolower($tech->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($tech->email) }}'.includes(search.toLowerCase()) || '{{ strtolower($tech->phone) }}'.includes(search.toLowerCase())">
                            <td class="p-1 align-top border-r border-gray-400">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($tech->name) }}&background=eee&color=555&size=32" class="rounded-full w-7 h-7" alt="Profile">
                            </td>
                            <td class="p-1 align-top border-r border-gray-400">{{ $tech->name }}</td>
                            <td class="p-1 align-top border-r border-gray-400">
                                <a href="mailto:{{ $tech->email }}" class="text-blue-700 underline">{{ $tech->email }}</a><br>
                                <a href="tel:{{ $tech->phone }}" class="text-gray-700">{{ $tech->phone }}</a>
                            </td>
                            <td class="p-1 align-top border-r border-gray-400">
                                <div class="relative">
                                    <button @click.prevent="
                                        dropdownOpen = true;
                                        dropdownTech = {{ $tech->id }};
                                        const rect = $event.target.getBoundingClientRect();
                                        dropdownTop = rect.bottom + window.scrollY;
                                        let left = rect.left + window.scrollX;
                                        const menuWidth = 150;
                                        if (left + menuWidth > window.innerWidth) {
                                            left = window.innerWidth - menuWidth - 8;
                                        }
                                        dropdownLeft = left;
                                    " class="px-2 py-1"><i class="fas fa-ellipsis-h"></i></button>
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
                        <a :href="'{{ url('m/technicians') }}/' + dropdownTech + '/edit'" class="block px-4 py-2 hover:bg-gray-100">Edit</a>
                        <a :href="'{{ url('m/technicians') }}/' + dropdownTech" class="block px-4 py-2 hover:bg-gray-100">View</a>
                        <form :action="'{{ url('m/technicians') }}/' + dropdownTech + '/deactivate'" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Deactivate</button>
                        </form>
                        <form :action="'{{ url('m/technicians') }}/' + dropdownTech + '/reset-password'" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Reset Password</button>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection 