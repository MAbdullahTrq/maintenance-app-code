@extends('mobile.layout')

@section('title', 'Properties')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div x-data="{ showForm: false, dropdownOpen: false, dropdownTop: 0, dropdownLeft: 0, dropdownProperty: null, dropdownAccessLink: '' }">
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg">All Properties</div>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-1 md:p-2 text-xs md:text-sm border-r border-gray-400">Name</th>
                            <th class="p-1 md:p-2 text-xs md:text-sm border-r border-gray-400">Address</th>
                            <th class="p-1 md:p-2 text-xs md:text-sm hidden md:table-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                        <tr class="border-b border-gray-400">
                            <td class="p-1 md:p-2 align-top border-r border-gray-400">
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($property->name) }}&background=eee&color=555&size=32" class="rounded-full w-7 h-7" alt="Profile">
                                    <a href="{{ route('mobile.properties.show', $property->id) }}" class="font-semibold text-blue-700">{{ $property->name }}</a>
                                </div>
                            </td>
                            <td class="p-1 md:p-2 align-top border-r border-gray-400">{{ $property->address }}</td>
                            <td class="p-1 md:p-2 align-top border-r border-gray-400 hidden md:table-cell">
                                <div class="relative">
                                    <button @click.prevent="
                                        dropdownOpen = true;
                                        dropdownProperty = {{ $property->id }};
                                        dropdownAccessLink = '{{ $property->access_link }}';
                                        const rect = $event.target.getBoundingClientRect();
                                        dropdownTop = rect.bottom + window.scrollY;
                                        dropdownLeft = rect.left + window.scrollX;
                                    " class="px-2 py-1"><i class="fas fa-ellipsis-v"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Single dropdown menu rendered outside the table -->
            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="fixed z-[9999] bg-white rounded shadow-lg border text-xs min-w-max" x-cloak :style="'top:'+dropdownTop+'px;left:'+dropdownLeft+'px;'">
                <template x-if="dropdownProperty">
                    <div>
                        <a :href="'{{ url('m/ep') }}/' + dropdownProperty" class="block px-4 py-2 hover:bg-gray-100">Edit</a>
                        <a :href="'/m/ap/' + dropdownProperty" class="block px-4 py-2 hover:bg-gray-100">View</a>
                        <a :href="'/m/ap/' + dropdownProperty + '/qrcode'" class="block px-4 py-2 hover:bg-gray-100">QR code</a>
                        <a :href="'/request/' + dropdownAccessLink" class="block px-4 py-2 hover:bg-gray-100">Link</a>
                    </div>
                </template>
            </div>
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('dropdownPropertyAccessLink', () => ({
                        @foreach($properties as $property)
                            {{ $property->id }}: "{{ $property->access_link }}",
                        @endforeach
                    }));
                });
            </script>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dropdownPortal() {
    return {
        open: false,
        top: 0,
        left: 0,
        toggle(e) {
            this.open = !this.open;
            if (this.open) {
                const rect = e.target.getBoundingClientRect();
                this.top = rect.bottom + window.scrollY;
                this.left = rect.right + window.scrollX - 140; // adjust for menu width
                this.$nextTick(() => {
                    document.getElementById('dropdown-menu-'+this._uid).style.display = 'block';
                });
            } else {
                document.getElementById('dropdown-menu-'+this._uid).style.display = 'none';
            }
        },
        close() {
            this.open = false;
            document.getElementById('dropdown-menu-'+this._uid).style.display = 'none';
        }
    }
}
</script>
@endpush

@if (!isset($__dropdown_menu_rendered))
    @php($__dropdown_menu_rendered = true)
    <div x-data="{}" x-init="window.addEventListener('click', function(e) { if (!e.target.closest('.dropdown-portal')) { document.querySelectorAll('.dropdown-portal-menu').forEach(el => el.style.display = 'none'); } })"></div>
@endif

@foreach($properties as $property)
    <div x-show="open" :id="'dropdown-menu-'+$id" class="dropdown-portal-menu fixed z-[9999] bg-white rounded shadow-lg border text-xs min-w-max" x-cloak style="display:none;" :style="'top:'+top+'px;left:'+left+'px;'">
        <a href="{{ route('mobile.properties.qrcode', $property->id) }}" class="block px-4 py-2 hover:bg-gray-100">QR Code</a>
        <a href="{{ route('guest.request.form', $property->access_link) }}" class="block px-4 py-2 hover:bg-gray-100">Link</a>
        <a :href="'{{ url('m/ep') }}/' + dropdownProperty" class="block px-4 py-2 hover:bg-gray-100">Edit</a>
    </div>
@endforeach 