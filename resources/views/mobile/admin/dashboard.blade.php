@extends('layouts.mobile')

@section('title', 'Admin Dashboard')

@section('nav-icons')
<div class="nav-icons">
    <a href="{{ route('mobile.admin.dashboard') }}" class="nav-icon">
        <i class="fas fa-tachometer-alt"></i>
        <div>Dashboard</div>
    </a>
    <a href="{{ route('admin.users.index') }}" class="nav-icon">
        <i class="fas fa-users"></i>
        <div>Users</div>
        <div class="count">{{ $propertyManagersCount + $techniciansCount + 1 }}</div>
    </a>
    <a href="{{ route('admin.subscription.plans.index') }}" class="nav-icon">
        <i class="fas fa-crown"></i>
        <div>Subscriptions</div>
        <div class="count">{{ $activeSubscriptionsCount }}</div>
    </a>
</div>
@endsection

@section('content')
<div style="padding: 15px;">
    <!-- Admin Stats Overview -->
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px;">Admin Dashboard</h1>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
            <div style="background-color: white; border-radius: 8px; padding: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
                <div style="font-size: 14px; margin-bottom: 5px;">Property Managers</div>
                <div style="font-size: 24px; font-weight: bold; color: #2563eb;">{{ $propertyManagersCount }}</div>
            </div>
            
            <div style="background-color: white; border-radius: 8px; padding: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
                <div style="font-size: 14px; margin-bottom: 5px;">Technicians</div>
                <div style="font-size: 24px; font-weight: bold; color: #2563eb;">{{ $techniciansCount }}</div>
            </div>
            
            <div style="background-color: white; border-radius: 8px; padding: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
                <div style="font-size: 14px; margin-bottom: 5px;">Properties</div>
                <div style="font-size: 24px; font-weight: bold; color: #2563eb;">{{ $propertiesCount }}</div>
            </div>
            
            <div style="background-color: white; border-radius: 8px; padding: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
                <div style="font-size: 14px; margin-bottom: 5px;">Total Requests</div>
                <div style="font-size: 24px; font-weight: bold; color: #2563eb;">{{ $totalRequestsCount }}</div>
            </div>
        </div>
    </div>
    
    <!-- Request Status -->
    <div style="background-color: white; border-radius: 8px; padding: 15px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">Request Status</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
            <div style="text-align: center;">
                <div style="font-size: 13px;">Pending</div>
                <div style="font-size: 20px; font-weight: bold; color: #f59e0b;">{{ $pendingRequestsCount }}</div>
            </div>
            
            <div style="text-align: center;">
                <div style="font-size: 13px;">In Progress</div>
                <div style="font-size: 20px; font-weight: bold; color: #3b82f6;">{{ $inProgressRequestsCount }}</div>
            </div>
            
            <div style="text-align: center;">
                <div style="font-size: 13px;">Completed</div>
                <div style="font-size: 20px; font-weight: bold; color: #10b981;">{{ $completedRequestsCount }}</div>
            </div>
        </div>
    </div>
    
    <!-- Subscription Management -->
    <div style="background-color: white; border-radius: 8px; padding: 15px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">Subscription Management</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div style="text-align: center;">
                <div style="font-size: 13px;">Active</div>
                <div style="font-size: 20px; font-weight: bold; color: #10b981;">{{ $activeSubscriptionsCount }}</div>
            </div>
            
            <div style="text-align: center;">
                <div style="font-size: 13px;">Expired</div>
                <div style="font-size: 20px; font-weight: bold; color: #ef4444;">{{ $expiredSubscriptionsCount }}</div>
            </div>
        </div>
        
        <div style="margin-top: 15px;">
            <a href="{{ route('admin.subscription.plans.index') }}" style="display: block; text-align: center; background-color: #f3f4f6; padding: 10px; border-radius: 6px; text-decoration: none; color: #4b5563; font-weight: 500;">
                Manage Subscription Plans
            </a>
        </div>
    </div>
    
    <!-- Recent Users -->
    <div style="background-color: white; border-radius: 8px; padding: 15px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">Active Users</h2>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 400px;">
                <thead>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <th style="text-align: left; padding: 8px 12px; font-weight: 500; color: #6b7280; font-size: 14px;">Name</th>
                        <th style="text-align: left; padding: 8px 12px; font-weight: 500; color: #6b7280; font-size: 14px;">Email</th>
                        <th style="text-align: left; padding: 8px 12px; font-weight: 500; color: #6b7280; font-size: 14px;">Role</th>
                        <th style="text-align: left; padding: 8px 12px; font-weight: 500; color: #6b7280; font-size: 14px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px; font-size: 14px;">{{ $user->name }}</td>
                        <td style="padding: 12px; font-size: 14px;">{{ $user->email }}</td>
                        <td style="padding: 12px; font-size: 14px;">{{ $user->role->name }}</td>
                        <td style="padding: 12px; font-size: 14px;">
                            <a href="{{ route('admin.users.show', $user) }}" style="color: #2563eb; text-decoration: none; margin-right: 8px;">View</a>
                            
                            @if($user->role->slug === 'property_manager')
                            <a href="{{ route('admin.users.grant-subscription.create', $user) }}" style="color: #10b981; text-decoration: none;">Grant Subscription</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 15px;">
            <a href="{{ route('admin.users.index') }}" style="display: block; text-align: center; background-color: #f3f4f6; padding: 10px; border-radius: 6px; text-decoration: none; color: #4b5563; font-weight: 500;">
                View All Users
            </a>
        </div>
    </div>
</div>
@endsection 