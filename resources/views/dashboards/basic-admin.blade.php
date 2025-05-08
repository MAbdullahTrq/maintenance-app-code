<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 16px;
            margin-bottom: 16px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }
        .col-span-2 {
            grid-column: span 2;
        }
        .stats-card {
            display: flex;
            align-items: center;
        }
        .stats-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
        }
        .stats-value {
            font-size: 24px;
            font-weight: bold;
        }
        .stats-label {
            color: #6b7280;
            font-size: 14px;
        }
        h1 {
            margin-top: 0;
            margin-bottom: 24px;
            font-size: 24px;
            color: #111827;
        }
        h2 {
            margin-top: 0;
            font-size: 18px;
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 12px 8px;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            background-color: #f9fafb;
        }
        td {
            padding: 12px 8px;
            border-top: 1px solid #e5e7eb;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-purple {
            background-color: #ede9fe;
            color: #5b21b6;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            margin-top: 8px;
        }
        .progress-value {
            height: 8px;
            border-radius: 4px;
        }
        .btn-link {
            color: #2563eb;
            text-decoration: none;
        }
        .btn-link:hover {
            text-decoration: underline;
        }
        .flex {
            display: flex;
        }
        .justify-between {
            justify-content: space-between;
        }
        .mt-4 {
            margin-top: 16px;
        }
        .space-y-4 > * + * {
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <!-- Stats Cards -->
        <div class="grid">
            <div class="card stats-card">
                <div class="stats-icon" style="background-color: rgba(59, 130, 246, 0.1);">
                    PM
                </div>
                <div>
                    <div class="stats-label">Property Managers</div>
                    <div class="stats-value">{{ $totalPropertyManagers }}</div>
                </div>
            </div>
            
            <div class="card stats-card">
                <div class="stats-icon" style="background-color: rgba(16, 185, 129, 0.1);">
                    T
                </div>
                <div>
                    <div class="stats-label">Technicians</div>
                    <div class="stats-value">{{ $totalTechnicians }}</div>
                </div>
            </div>
            
            <div class="card stats-card">
                <div class="stats-icon" style="background-color: rgba(139, 92, 246, 0.1);">
                    P
                </div>
                <div>
                    <div class="stats-label">Properties</div>
                    <div class="stats-value">{{ $totalProperties }}</div>
                </div>
            </div>
            
            <div class="card stats-card">
                <div class="stats-icon" style="background-color: rgba(245, 158, 11, 0.1);">
                    R
                </div>
                <div>
                    <div class="stats-label">Total Requests</div>
                    <div class="stats-value">{{ $totalRequests }}</div>
                </div>
            </div>
        </div>

        <div class="grid" style="grid-template-columns: 2fr 1fr;">
            <!-- Active Users Table -->
            <div class="card">
                <h2>Active Users</h2>
                
                @if($activeUsers->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeUsers as $user)
                                <tr>
                                    <td>
                                        {{ $user->name }}
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        @if($user->role->slug == 'admin')
                                            <span class="badge badge-purple">
                                                Admin
                                            </span>
                                        @elseif($user->role->slug == 'property_manager')
                                            <span class="badge badge-blue">
                                                Property Manager
                                            </span>
                                        @else
                                            <span class="badge badge-green">
                                                Technician
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn-link">View</a>
                                        
                                        @if($user->role->slug === 'property_manager')
                                            <a href="{{ route('admin.users.grant-subscription.create', $user) }}" class="btn-link" style="margin-left: 8px;">Grant Subscription</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4" style="text-align: right;">
                        <a href="{{ route('admin.users.index') }}" class="btn-link">
                            View All Users →
                        </a>
                    </div>
                @else
                    <p>No active users found.</p>
                @endif
            </div>
            
            <!-- Sidebar Sections -->
            <div class="space-y-4">
                <!-- Request Status -->
                <div class="card">
                    <h2>Request Status</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between">
                                <span style="font-size: 14px; font-weight: 500;">Pending</span>
                                <span style="font-size: 14px; font-weight: 500;">{{ $pendingRequests }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-value" style="width: {{ $totalRequests > 0 ? ($pendingRequests / $totalRequests * 100) : 0 }}%; background-color: #f59e0b;"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between">
                                <span style="font-size: 14px; font-weight: 500;">In Progress</span>
                                <span style="font-size: 14px; font-weight: 500;">{{ $inProgressRequests }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-value" style="width: {{ $totalRequests > 0 ? ($inProgressRequests / $totalRequests * 100) : 0 }}%; background-color: #3b82f6;"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between">
                                <span style="font-size: 14px; font-weight: 500;">Completed</span>
                                <span style="font-size: 14px; font-weight: 500;">{{ $completedRequests }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-value" style="width: {{ $totalRequests > 0 ? ($completedRequests / $totalRequests * 100) : 0 }}%; background-color: #10b981;"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between">
                                <span style="font-size: 14px; font-weight: 500;">Closed</span>
                                <span style="font-size: 14px; font-weight: 500;">{{ $closedRequests }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-value" style="width: {{ $totalRequests > 0 ? ($closedRequests / $totalRequests * 100) : 0 }}%; background-color: #6b7280;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Management -->
                <div class="card">
                    <h2>Subscription Management</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between">
                                <span style="font-size: 14px; font-weight: 500;">Active Subscriptions</span>
                                <span style="font-size: 14px; font-weight: 500;">{{ $activeSubscriptions ?? 0 }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-value" style="width: {{ isset($totalPropertyManagers) && $totalPropertyManagers > 0 ? (($activeSubscriptions ?? 0) / $totalPropertyManagers * 100) : 0 }}%; background-color: #10b981;"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between">
                                <span style="font-size: 14px; font-weight: 500;">Expired Subscriptions</span>
                                <span style="font-size: 14px; font-weight: 500;">{{ $expiredSubscriptions ?? 0 }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-value" style="width: {{ isset($totalPropertyManagers) && $totalPropertyManagers > 0 ? (($expiredSubscriptions ?? 0) / $totalPropertyManagers * 100) : 0 }}%; background-color: #ef4444;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 style="font-size: 14px; font-weight: 500; margin-bottom: 8px;">Quick Actions</h3>
                        <div class="space-y-2">
                            <div>
                                <a href="{{ route('admin.users.index') }}" class="btn-link">Manage Users</a>
                            </div>
                            <div>
                                <a href="{{ route('admin.subscription.plans.index') }}" class="btn-link">Manage Plans</a>
                            </div>
                            <div>
                                <a href="{{ route('admin.subscription.index') }}" class="btn-link">View All Subscriptions</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 