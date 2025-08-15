@extends('mobile.layout')

@section('title', 'Dash – Technician')

@section('header-actions')
<a href="#" class="text-sm font-medium">Technician &gt;</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-6 w-full max-w-6xl mx-auto" x-data="{ 
        activeTab: 'assigned', 
        search: '',
        sortBy: 'created_at',
        sortDirection: 'desc',
        
        assignedRequests: @js($assignedRequests->toArray()),
        acceptedRequests: @js($acceptedRequests->toArray()),
        completedRequests: @js($completedRequests->toArray()),
        allRequests: @js($allRequests->toArray()),
        
        getPriorityValue(priority) {
            const priorityOrder = { 'high': 3, 'medium': 2, 'low': 1 };
            return priorityOrder[priority?.toLowerCase()] || 0;
        },
        
        getSortedRequests(requests) {
            let sorted = [...requests];
            return sorted.sort((a, b) => {
                let aVal, bVal;
                
                if (this.sortBy === 'created_at') {
                    aVal = new Date(a.created_at);
                    bVal = new Date(b.created_at);
                } else if (this.sortBy === 'priority') {
                    aVal = this.getPriorityValue(a.priority);
                    bVal = this.getPriorityValue(b.priority);
                }
                
                if (this.sortDirection === 'desc') {
                    return aVal > bVal ? -1 : 1;
                } else {
                    return aVal > bVal ? 1 : -1;
                }
            });
        },
        
        getFilteredRequests(requests) {
            if (!this.search) return this.getSortedRequests(requests);
            
            const filtered = requests.filter(req => {
                const propertyName = req.property?.name?.toLowerCase() || '';
                const propertyAddress = req.property?.address?.toLowerCase() || '';
                const searchTerm = this.search.toLowerCase();
                return propertyName.includes(searchTerm) || propertyAddress.includes(searchTerm);
            });
            
            return this.getSortedRequests(filtered);
        },
        
        toggleSort(column) {
            if (this.sortBy === column) {
                this.sortDirection = this.sortDirection === 'desc' ? 'asc' : 'desc';
            } else {
                this.sortBy = column;
                this.sortDirection = 'desc';
            }
        }
    }">
        <div class="grid grid-cols-4 gap-4 mb-6">
            <button :class="activeTab === 'assigned' ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-700'" class="text-center rounded py-3 md:py-4 font-bold focus:outline-none transition" @click="activeTab = 'assigned'">
                <div class="text-xs md:text-sm lg:text-base">Assigned</div>
                <div class="text-2xl md:text-3xl lg:text-4xl">{{ $assignedCount }}</div>
            </button>
            <button :class="activeTab === 'accepted' ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-700'" class="text-center rounded py-3 md:py-4 font-bold focus:outline-none transition" @click="activeTab = 'accepted'">
                <div class="text-xs md:text-sm lg:text-base">Accepted</div>
                <div class="text-2xl md:text-3xl lg:text-4xl">{{ $acceptedCount }}</div>
            </button>
            <button :class="activeTab === 'completed' ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-700'" class="text-center rounded py-3 md:py-4 font-bold focus:outline-none transition" @click="activeTab = 'completed'">
                <div class="text-xs md:text-sm lg:text-base">Completed</div>
                <div class="text-2xl md:text-3xl lg:text-4xl">{{ $completedCount }}</div>
            </button>
            <button :class="activeTab === 'requests' ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-700'" class="text-center rounded py-3 md:py-4 font-bold focus:outline-none transition" @click="activeTab = 'requests'">
                <div class="text-xs md:text-sm lg:text-base">Requests</div>
                <div class="text-2xl md:text-3xl lg:text-4xl">{{ $allCount }}</div>
            </button>
        </div>
        <div class="text-center font-bold text-xl md:text-2xl lg:text-3xl mb-4" x-text="activeTab.charAt(0).toUpperCase() + activeTab.slice(1)"></div>
        <div class="mb-4">
            <input type="text" placeholder="Search" class="w-full border rounded p-3 text-sm md:text-base lg:text-lg" x-model="search">
        </div>
        <div class="overflow-x-auto w-full">
            <template x-if="activeTab === 'assigned'">
                <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden table-fixed">
                    <colgroup>
                        <col class="w-2/5">
                        <col class="w-1/6">
                        <col class="w-1/6">
                        <col class="w-1/12">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                                <button @click="toggleSort('priority')" class="flex items-center justify-center hover:text-blue-600 w-full">
                                    Priority
                                    <span class="ml-1" x-text="sortBy === 'priority' ? (sortDirection === 'desc' ? '↓' : '↑') : '↓'"></span>
                                </button>
                            </th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                                <button @click="toggleSort('created_at')" class="flex items-center justify-center hover:text-blue-600 w-full">
                                    Date
                                    <span class="ml-1" x-text="sortBy === 'created_at' ? (sortDirection === 'desc' ? '↓' : '↑') : '↓'"></span>
                                </button>
                            </th>
                            <th class="p-2 md:p-3 lg:p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="req in getFilteredRequests(assignedRequests)" :key="req.id">
                            <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" @click="window.location.href='/t/r/' + req.id">
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                    <div class="font-semibold" x-text="req.property?.name || ''"></div>
                                    <div class="text-xs md:text-sm text-blue-700 underline">
                                        <template x-if="req.property?.address">
                                            <div>
                                                <span class="md:hidden" x-text="(req.property.address.length > 15) ? req.property.address.substring(0, 15) + '...' : req.property.address"></span>
                                                <span class="hidden md:block" x-text="(req.property.address.length > 30) ? req.property.address.substring(0, 30) + '...' : req.property.address"></span>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center" 
                                    :class="req.priority?.toLowerCase() === 'high' ? 'bg-red-200' : (req.priority?.toLowerCase() === 'low' ? 'bg-blue-200' : (req.priority?.toLowerCase() === 'medium' ? 'bg-yellow-200' : ''))"
                                    :style="req.priority?.toLowerCase() === 'high' ? 'background-color: #fecaca;' : (req.priority?.toLowerCase() === 'low' ? 'background-color: #bfdbfe;' : (req.priority?.toLowerCase() === 'medium' ? 'background-color: #fde68a;' : ''))">
                                    <span x-text="req.priority ? req.priority.charAt(0).toUpperCase() + req.priority.slice(1) : ''"></span>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                                    <div x-text="req.created_at ? new Date(req.created_at).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'}) : '-'"></div>
                                    <div class="text-xs text-gray-500" x-text="req.created_at ? new Date(req.created_at).toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'}) : '-'"></div>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                                    <a :href="'/t/r/' + req.id" class="inline-block text-blue-600 text-lg md:text-xl" @click.stop><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
            <template x-if="activeTab === 'accepted'">
                <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden table-fixed">
                    <colgroup>
                        <col class="w-2/5">
                        <col class="w-1/6">
                        <col class="w-1/6">
                        <col class="w-1/12">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                                <button @click="toggleSort('priority')" class="flex items-center justify-center hover:text-blue-600 w-full">
                                    Priority
                                    <span class="ml-1" x-text="sortBy === 'priority' ? (sortDirection === 'desc' ? '↓' : '↑') : '↓'"></span>
                                </button>
                            </th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                                <button @click="toggleSort('created_at')" class="flex items-center justify-center hover:text-blue-600 w-full">
                                    Date
                                    <span class="ml-1" x-text="sortBy === 'created_at' ? (sortDirection === 'desc' ? '↓' : '↑') : '↓'"></span>
                                </button>
                            </th>
                            <th class="p-2 md:p-3 lg:p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="req in getFilteredRequests(acceptedRequests)" :key="req.id">
                            <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" @click="window.location.href='/t/r/' + req.id">
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                    <div class="font-semibold" x-text="req.property?.name || ''"></div>
                                    <div class="text-xs md:text-sm text-blue-700 underline">
                                        <template x-if="req.property?.address">
                                            <div>
                                                <span class="md:hidden" x-text="(req.property.address.length > 15) ? req.property.address.substring(0, 15) + '...' : req.property.address"></span>
                                                <span class="hidden md:block" x-text="(req.property.address.length > 30) ? req.property.address.substring(0, 30) + '...' : req.property.address"></span>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center" 
                                    :class="req.priority?.toLowerCase() === 'high' ? 'bg-red-200' : (req.priority?.toLowerCase() === 'low' ? 'bg-blue-200' : (req.priority?.toLowerCase() === 'medium' ? 'bg-yellow-200' : ''))"
                                    :style="req.priority?.toLowerCase() === 'high' ? 'background-color: #fecaca;' : (req.priority?.toLowerCase() === 'low' ? 'background-color: #bfdbfe;' : (req.priority?.toLowerCase() === 'medium' ? 'background-color: #fde68a;' : ''))">
                                    <span x-text="req.priority ? req.priority.charAt(0).toUpperCase() + req.priority.slice(1) : ''"></span>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                                    <div x-text="req.created_at ? new Date(req.created_at).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'}) : '-'"></div>
                                    <div class="text-xs text-gray-500" x-text="req.created_at ? new Date(req.created_at).toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'}) : '-'"></div>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                                    <a :href="'/t/r/' + req.id" class="inline-block text-blue-600 text-lg md:text-xl" @click.stop><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
            <template x-if="activeTab === 'completed'">
                <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden table-fixed">
                    <colgroup>
                        <col class="w-2/5">
                        <col class="w-1/6">
                        <col class="w-1/6">
                        <col class="w-1/12">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                                <button @click="toggleSort('priority')" class="flex items-center justify-center hover:text-blue-600 w-full">
                                    Priority
                                    <span class="ml-1" x-text="sortBy === 'priority' ? (sortDirection === 'desc' ? '↓' : '↑') : '↓'"></span>
                                </button>
                            </th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                                <button @click="toggleSort('created_at')" class="flex items-center justify-center hover:text-blue-600 w-full">
                                    Date
                                    <span class="ml-1" x-text="sortBy === 'created_at' ? (sortDirection === 'desc' ? '↓' : '↑') : '↓'"></span>
                                </button>
                            </th>
                            <th class="p-2 md:p-3 lg:p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="req in getFilteredRequests(completedRequests)" :key="req.id">
                            <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" @click="window.location.href='/t/r/' + req.id">
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                    <div class="font-semibold" x-text="req.property?.name || ''"></div>
                                    <div class="text-xs md:text-sm text-blue-700 underline">
                                        <template x-if="req.property?.address">
                                            <div>
                                                <span class="md:hidden" x-text="(req.property.address.length > 15) ? req.property.address.substring(0, 15) + '...' : req.property.address"></span>
                                                <span class="hidden md:block" x-text="(req.property.address.length > 30) ? req.property.address.substring(0, 30) + '...' : req.property.address"></span>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center" 
                                    :class="req.priority?.toLowerCase() === 'high' ? 'bg-red-200' : (req.priority?.toLowerCase() === 'low' ? 'bg-blue-200' : (req.priority?.toLowerCase() === 'medium' ? 'bg-yellow-200' : ''))"
                                    :style="req.priority?.toLowerCase() === 'high' ? 'background-color: #fecaca;' : (req.priority?.toLowerCase() === 'low' ? 'background-color: #bfdbfe;' : (req.priority?.toLowerCase() === 'medium' ? 'background-color: #fde68a;' : ''))">
                                    <span x-text="req.priority ? req.priority.charAt(0).toUpperCase() + req.priority.slice(1) : ''"></span>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                                    <div x-text="req.created_at ? new Date(req.created_at).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'}) : '-'"></div>
                                    <div class="text-xs text-gray-500" x-text="req.created_at ? new Date(req.created_at).toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'}) : '-'"></div>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                                    <a :href="'/t/r/' + req.id" class="inline-block text-blue-600 text-lg md:text-xl" @click.stop><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
            <template x-if="activeTab === 'requests'">
                <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden table-fixed">
                    <colgroup>
                        <col class="w-2/5">
                        <col class="w-1/6">
                        <col class="w-1/6">
                        <col class="w-1/12">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                                <button @click="toggleSort('priority')" class="flex items-center justify-center hover:text-blue-600 w-full">
                                    Priority
                                    <span class="ml-1" x-text="sortBy === 'priority' ? (sortDirection === 'desc' ? '↓' : '↑') : '↓'"></span>
                                </button>
                            </th>
                            <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400">
                                <button @click="toggleSort('created_at')" class="flex items-center justify-center hover:text-blue-600 w-full">
                                    Date
                                    <span class="ml-1" x-text="sortBy === 'created_at' ? (sortDirection === 'desc' ? '↓' : '↑') : '↓'"></span>
                                </button>
                            </th>
                            <th class="p-2 md:p-3 lg:p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="req in getFilteredRequests(allRequests)" :key="req.id">
                            <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" @click="window.location.href='/t/r/' + req.id">
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                                    <div class="font-semibold" x-text="req.property?.name || ''"></div>
                                    <div class="text-xs md:text-sm text-blue-700 underline">
                                        <template x-if="req.property?.address">
                                            <div>
                                                <span class="md:hidden" x-text="(req.property.address.length > 15) ? req.property.address.substring(0, 15) + '...' : req.property.address"></span>
                                                <span class="hidden md:block" x-text="(req.property.address.length > 30) ? req.property.address.substring(0, 30) + '...' : req.property.address"></span>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center" 
                                    :class="req.priority?.toLowerCase() === 'high' ? 'bg-red-200' : (req.priority?.toLowerCase() === 'low' ? 'bg-blue-200' : (req.priority?.toLowerCase() === 'medium' ? 'bg-yellow-200' : ''))"
                                    :style="req.priority?.toLowerCase() === 'high' ? 'background-color: #fecaca;' : (req.priority?.toLowerCase() === 'low' ? 'background-color: #bfdbfe;' : (req.priority?.toLowerCase() === 'medium' ? 'background-color: #fde68a;' : ''))">
                                    <span x-text="req.priority ? req.priority.charAt(0).toUpperCase() + req.priority.slice(1) : ''"></span>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                                    <div x-text="req.created_at ? new Date(req.created_at).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'}) : '-'"></div>
                                    <div class="text-xs text-gray-500" x-text="req.created_at ? new Date(req.created_at).toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'}) : '-'"></div>
                                </td>
                                <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                                    <a :href="'/t/r/' + req.id" class="inline-block text-blue-600 text-lg md:text-xl" @click.stop><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </div>
    </div>
</div>
@endsection 