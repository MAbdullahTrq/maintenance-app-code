<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Owner;
use App\Models\Property;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only allow property managers and admins
        if (!$user->isPropertyManager() && !$user->isAdmin()) {
            abort(403);
        }

        // Get data for dropdowns
        if ($user->isAdmin()) {
            $owners = Owner::all();
            $properties = Property::with('owner')->get();
            $technicians = User::whereHas('role', function($q) {
                $q->where('slug', 'technician');
            })->get();
        } else {
            // Property managers see only their managed owners and properties
            $owners = $user->managedOwners()->get();
            $properties = $user->managedProperties()->with('owner')->get();
            $technicians = User::whereHas('role', function($q) {
                $q->where('slug', 'technician');
            })->where('invited_by', $user->id)->get();
        }

        return view('reports.create', compact('owners', 'properties', 'technicians'));
    }

    /**
     * Show the mobile form for creating a new report.
     */
    public function createMobile()
    {
        $user = Auth::user();
        
        // Only allow property managers and admins
        if (!$user->isPropertyManager() && !$user->isAdmin()) {
            abort(403);
        }

        // Get data for dropdowns
        if ($user->isAdmin()) {
            $owners = Owner::all();
            $properties = Property::with('owner')->get();
            $technicians = User::whereHas('role', function($q) {
                $q->where('slug', 'technician');
            })->get();
        } else {
            // Property managers see only their managed owners and properties
            $owners = $user->managedOwners()->get();
            $properties = $user->managedProperties()->with('owner')->get();
            $technicians = User::whereHas('role', function($q) {
                $q->where('slug', 'technician');
            })->where('invited_by', $user->id)->get();
        }

        return view('mobile.reports.create', compact('owners', 'properties', 'technicians'));
    }

    /**
     * Get properties for a specific owner (AJAX endpoint).
     */
    public function getPropertiesByOwner(Request $request)
    {
        $ownerId = $request->owner_id;
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $properties = Property::where('owner_id', $ownerId)->get();
        } else {
            // Property managers can only see their own managed properties for the selected owner
            $properties = $user->managedProperties()
                ->where('owner_id', $ownerId)
                ->get();
        }

        return response()->json($properties);
    }

    /**
     * Get technicians who have worked on specific properties (AJAX endpoint).
     */
    public function getTechniciansByProperties(Request $request)
    {
        $propertyIds = $request->property_ids;
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $technicians = User::whereHas('role', function($q) {
                $q->where('slug', 'technician');
            })->whereHas('assignedRequests', function($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds);
            })->get();
        } else {
            // Property managers see only their technicians
            $technicians = User::whereHas('role', function($q) {
                $q->where('slug', 'technician');
            })->where('invited_by', $user->id)
            ->whereHas('assignedRequests', function($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds);
            })->get();
        }

        return response()->json($technicians);
    }

    /**
     * Generate and display the report.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'date_range' => 'required|string',
            'owner_id' => 'nullable|exists:owners,id',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
            'technician_ids' => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
            'format' => 'nullable|in:web,pdf,csv'
        ]);

        $user = Auth::user();
        $format = $request->input('format', 'web');
        
        // Parse date range
        $dateRange = $this->parseDateRange($request->date_range);
        
        // Build query based on filters
        $query = $this->buildReportQuery($request, $user, $dateRange);
        
        // Get the results
        $requests = $query->get();
        
        // Generate report data
        $reportData = $this->generateReportData($requests, $request, $dateRange);
        
        // Handle different output formats
        switch ($format) {
            case 'pdf':
                return $this->generatePDF($reportData);
            case 'csv':
                return $this->generateCSV($reportData);
            default:
                return $this->displayWebReport($reportData, $request);
        }
    }

    /**
     * Generate mobile report.
     */
    public function generateMobile(Request $request)
    {
        // Check if requesting a download format
        $format = $request->input('format', 'web');
        
        if (in_array($format, ['pdf', 'csv'])) {
            // For downloads, call generate directly and return the response
            return $this->generate($request);
        }
        
        // For web view, build the report data and return mobile template
        $user = Auth::user();
        
        // Parse date range
        $dateRange = $this->parseDateRange($request->date_range);
        
        // Build query based on filters
        $query = $this->buildReportQuery($request, $user, $dateRange);
        
        // Get the results
        $requests = $query->get();
        
        // Generate report data
        $reportData = $this->generateReportData($requests, $request, $dateRange);
        
        return view('mobile.reports.show', $reportData);
    }

    /**
     * Parse date range input.
     */
    private function parseDateRange(string $dateRange): array
    {
        switch ($dateRange) {
            case 'last_7_days':
                return [
                    'start' => Carbon::now()->subDays(7)->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'label' => 'Last 7 Days'
                ];
            case 'this_month':
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                    'label' => 'This Month'
                ];
            case 'last_month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth(),
                    'label' => 'Last Month'
                ];
            case 'this_year':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear(),
                    'label' => 'This Year'
                ];
            default:
                // Handle custom date range "YYYY-MM-DD to YYYY-MM-DD"
                if (preg_match('/^(\d{4}-\d{2}-\d{2}) to (\d{4}-\d{2}-\d{2})$/', $dateRange, $matches)) {
                    return [
                        'start' => Carbon::parse($matches[1])->startOfDay(),
                        'end' => Carbon::parse($matches[2])->endOfDay(),
                        'label' => 'Custom Range'
                    ];
                }
                
                // Default to last 30 days
                return [
                    'start' => Carbon::now()->subDays(30)->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'label' => 'Last 30 Days'
                ];
        }
    }

    /**
     * Build the maintenance request query based on filters.
     */
    private function buildReportQuery(Request $request, $user, array $dateRange)
    {
        $query = MaintenanceRequest::with(['property.owner', 'assignedTechnician'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        // Apply user permissions
        if (!$user->isAdmin()) {
            $propertyIds = $user->managedProperties()->pluck('id');
            $query->whereIn('property_id', $propertyIds);
        }

        // Apply filters
        if ($request->filled('owner_id')) {
            $query->whereHas('property', function($q) use ($request) {
                $q->where('owner_id', $request->owner_id);
            });
        }

        if ($request->filled('property_ids')) {
            $query->whereIn('property_id', $request->property_ids);
        }

        if ($request->filled('technician_ids')) {
            $query->whereIn('assigned_to', $request->technician_ids);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Generate comprehensive report data.
     */
    private function generateReportData($requests, Request $request, array $dateRange): array
    {
        // Basic stats
        $totalRequests = $requests->count();
        $completedRequests = $requests->where('status', 'completed')->count();
        $pendingRequests = $requests->where('status', 'pending')->count();
        $averageCompletionTime = $this->calculateAverageCompletionTime($requests);

        // Group by status
        $statusBreakdown = $requests->groupBy('status')->map(function($group) {
            return $group->count();
        });

        // Group by priority
        $priorityBreakdown = $requests->groupBy('priority')->map(function($group) {
            return $group->count();
        });

        // Group by property
        $propertyBreakdown = $requests->groupBy('property.name')->map(function($group) {
            return [
                'count' => $group->count(),
                'completed' => $group->where('status', 'completed')->count()
            ];
        });

        // Group by technician
        $technicianBreakdown = $requests->whereNotNull('assigned_to')
            ->groupBy('assignedTechnician.name')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'completed' => $group->where('status', 'completed')->count(),
                    'avg_completion_time' => $this->calculateAverageCompletionTime($group)
                ];
            });

        return [
            'requests' => $requests,
            'filters' => $request->all(),
            'dateRange' => $dateRange,
            'summary' => [
                'total_requests' => $totalRequests,
                'completed_requests' => $completedRequests,
                'pending_requests' => $pendingRequests,
                'completion_rate' => $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100, 1) : 0,
                'average_completion_time' => $averageCompletionTime
            ],
            'breakdowns' => [
                'status' => $statusBreakdown,
                'priority' => $priorityBreakdown,
                'property' => $propertyBreakdown,
                'technician' => $technicianBreakdown
            ],
            'report_type' => $this->determineReportType($request)
        ];
    }

    /**
     * Calculate average completion time in hours.
     */
    private function calculateAverageCompletionTime($requests): ?float
    {
        $completedRequests = $requests->where('status', 'completed')
            ->whereNotNull('completed_at');

        if ($completedRequests->isEmpty()) {
            return null;
        }

        $totalHours = $completedRequests->sum(function($request) {
            return $request->created_at->diffInHours($request->completed_at);
        });

        return round($totalHours / $completedRequests->count(), 1);
    }

    /**
     * Determine the type of report being generated.
     */
    private function determineReportType(Request $request): string
    {
        if ($request->filled('owner_id') && !$request->filled('property_ids') && !$request->filled('technician_ids')) {
            return 'All work across owner\'s properties';
        }
        
        if ($request->filled('owner_id') && $request->filled('property_ids') && !$request->filled('technician_ids')) {
            return 'Specific property under an owner';
        }
        
        if (!$request->filled('owner_id') && $request->filled('property_ids') && !$request->filled('technician_ids')) {
            return 'Property-based report';
        }
        
        if (!$request->filled('owner_id') && $request->filled('property_ids') && $request->filled('technician_ids')) {
            return 'Tasks by a tech at a specific property';
        }
        
        if (!$request->filled('owner_id') && !$request->filled('property_ids') && $request->filled('technician_ids')) {
            return 'All work by a technician';
        }
        
        return 'Full system report';
    }

    /**
     * Display web report.
     */
    private function displayWebReport(array $reportData, Request $request)
    {
        if ($request->is('*/m/*')) {
            return view('mobile.reports.show', $reportData);
        }
        
        return view('reports.show', $reportData);
    }

    /**
     * Generate PDF report.
     */
    private function generatePDF(array $reportData)
    {
        // Create a special PDF view that opens in new window and triggers print dialog
        return view('reports.pdf', $reportData);
    }

    /**
     * Generate CSV report.
     */
    private function generateCSV(array $reportData)
    {
        $filename = 'maintenance_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function() use ($reportData) {
            $handle = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($handle, [
                'ID', 'Title', 'Property', 'Owner', 'Priority', 'Status', 
                'Technician', 'Created Date', 'Completed Date', 'Completion Time (Hours)'
            ]);
            
            // CSV Data
            foreach ($reportData['requests'] as $request) {
                $completionTime = null;
                if ($request->completed_at) {
                    $completionTime = $request->created_at->diffInHours($request->completed_at);
                }
                
                fputcsv($handle, [
                    $request->id,
                    $request->title,
                    $request->property->name ?? '',
                    $request->property->owner->name ?? '',
                    $request->priority,
                    $request->status,
                    $request->assignedTechnician->name ?? 'Not Assigned',
                    $request->created_at->format('Y-m-d H:i:s'),
                    $request->completed_at ? $request->completed_at->format('Y-m-d H:i:s') : '',
                    $completionTime
                ]);
            }
            
            fclose($handle);
        }, 200, $headers);
    }
} 