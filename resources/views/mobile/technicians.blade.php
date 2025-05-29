@extends('mobile.layout')

@section('title', 'Technicians')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div x-data="{ showForm: false, search: '', dropdownOpen: false, dropdownTop: 0, dropdownLeft: 0, dropdownTech: null }">
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg">All Technicians</div>
            </div>
            <input type="text" x-model="search" placeholder="Search" class="w-full border rounded p-2 mb-4" />
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs md:text-sm border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-1 md:p-2 border-r border-gray-400"> </th>
                            <th class="p-1 md:p-2 border-r border-gray-400">Name</th>
                            <th class="p-1 md:p-2 border-r border-gray-400">Address</th>
                            <th class="p-1 md:p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicians as $tech)
                        <tr class="border-b border-gray-400" x-show="!search || '{{ strtolower($tech->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($tech->email) }}'.includes(search.toLowerCase()) || '{{ strtolower($tech->phone) }}'.includes(search.toLowerCase())">
                            <td class="p-1 md:p-2 align-top border-r border-gray-400">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($tech->name) }}&background=eee&color=555&size=32" class="rounded-full w-7 h-7" alt="Profile">
                            </td>
                            <td class="p-1 md:p-2 align-top border-r border-gray-400">{{ $tech->name }}</td>
                            <td class="p-1 md:p-2 align-top border-r border-gray-400">
                                <a href="mailto:{{ $tech->email }}" class="text-blue-700 underline">{{ $tech->email }}</a><br>
                                <a href="tel:{{ $tech->phone }}" class="text-gray-700">{{ $tech->phone }}</a>
                            </td>
                            <td class="p-1 md:p-2 align-top border-r border-gray-400">
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
                        <a :href="'{{ url('m/at') }}/' + dropdownTech + '/edit'" class="block px-4 py-2 hover:bg-gray-100">Edit</a>
                        <a :href="'{{ url('m/at') }}/' + dropdownTech" class="block px-4 py-2 hover:bg-gray-100">View</a>
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
<a href="{{ route('mobile.technicians.create') }}" class="fixed bottom-6 right-6 bg-blue-600 text-white rounded-full w-14 h-14 flex items-center justify-center text-3xl shadow-lg z-50">
    +
</a>
@endsection 