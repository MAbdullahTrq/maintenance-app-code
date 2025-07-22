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
use OpenAI\Laravel\Facades\OpenAI;

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

        // Add navigation stats for mobile layout
        if ($user->isAdmin()) {
            $navigationStats = [
                'ownersCount' => \App\Models\Owner::count(),
                'propertiesCount' => \App\Models\Property::count(),
                'techniciansCount' => \App\Models\User::whereHas('role', function($q) {
                    $q->where('slug', 'technician');
                })->count(),
                'requestsCount' => \App\Models\MaintenanceRequest::count(),
            ];
        } else {
            // Property manager stats
            $navigationStats = [
                'ownersCount' => $user->managedOwners()->count(),
                'propertiesCount' => $user->managedProperties()->count(),
                'techniciansCount' => \App\Models\User::whereHas('role', function($q) {
                    $q->where('slug', 'technician');
                })->where('invited_by', $user->id)->count(),
                'requestsCount' => \App\Models\MaintenanceRequest::whereIn('property_id', $user->managedProperties()->pluck('id'))->count(),
            ];
        }

        return view('mobile.reports.create', compact('owners', 'properties', 'technicians') + $navigationStats);
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
     * Generate report based on form input.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'date_range' => 'required|string',
            'owner_id' => 'nullable|exists:owners,id',
            'property_id' => 'nullable|exists:properties,id',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
            'technician_id' => 'nullable|exists:users,id',
            'technician_ids' => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
            'format' => 'nullable|in:web,pdf,csv'
        ]);

        $user = Auth::user();
        $format = $request->input('format', 'web');
        
        // Convert single selections to arrays for backward compatibility
        $this->normalizeSingleSelections($request);
        
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
        // Convert single selections to arrays for backward compatibility
        $this->normalizeSingleSelections($request);
        
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
        
        // Add owner, properties, and technicians details for mobile report title
        $ownerName = null;
        $propertyNames = [];
        $technicianNames = [];
        
        if ($request->filled('owner_id')) {
            $owner = \App\Models\Owner::find($request->owner_id);
            $ownerName = $owner ? $owner->name : null;
        }
        
        if ($request->filled('property_ids')) {
            $propertyNames = \App\Models\Property::whereIn('id', $request->property_ids)
                ->pluck('name')
                ->toArray();
        }
        
        if ($request->filled('technician_ids')) {
            $technicianNames = \App\Models\User::whereIn('id', $request->technician_ids)
                ->pluck('name')
                ->toArray();
        }
        
        // Add navigation stats for mobile layout
        if ($user->isAdmin()) {
            $navigationStats = [
                'ownersCount' => \App\Models\Owner::count(),
                'propertiesCount' => \App\Models\Property::count(),
                'techniciansCount' => \App\Models\User::whereHas('role', function($q) {
                    $q->where('slug', 'technician');
                })->count(),
                'requestsCount' => \App\Models\MaintenanceRequest::count(),
            ];
        } else {
            // Property manager stats
            $navigationStats = [
                'ownersCount' => $user->managedOwners()->count(),
                'propertiesCount' => $user->managedProperties()->count(),
                'techniciansCount' => \App\Models\User::whereHas('role', function($q) {
                    $q->where('slug', 'technician');
                })->where('invited_by', $user->id)->count(),
                'requestsCount' => \App\Models\MaintenanceRequest::whereIn('property_id', $user->managedProperties()->pluck('id'))->count(),
            ];
        }
        
        // Merge all data for mobile report
        $reportData = array_merge($reportData, $navigationStats, [
            'owner_name' => $ownerName,
            'property_names' => $propertyNames,
            'technician_names' => $technicianNames,
        ]);
        
        return view('mobile.reports.show', $reportData);
    }

    /**
     * Generate and download CSV report.
     */
    public function generateCSVReport(Request $request)
    {
        $request->validate([
            'date_range' => 'required|string',
            'owner_id' => 'nullable|exists:owners,id',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
            'technician_ids' => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
        ]);

        $user = Auth::user();
        
        // Parse date range
        $dateRange = $this->parseDateRange($request->date_range);
        
        // Build query based on filters
        $query = $this->buildReportQuery($request, $user, $dateRange);
        
        // Get the results
        $requests = $query->get();
        
        // Generate report data
        $reportData = $this->generateReportData($requests, $request, $dateRange);
        
        return $this->generateCSV($reportData);
    }

    /**
     * Generate and show PDF report.
     */
    public function generatePDFReport(Request $request)
    {
        $request->validate([
            'date_range' => 'required|string',
            'owner_id' => 'nullable|exists:owners,id',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
            'technician_ids' => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
        ]);

        $user = Auth::user();
        
        // Parse date range
        $dateRange = $this->parseDateRange($request->date_range);
        
        // Build query based on filters
        $query = $this->buildReportQuery($request, $user, $dateRange);
        
        // Get the results
        $requests = $query->get();
        
        // Generate report data
        $reportData = $this->generateReportData($requests, $request, $dateRange);
        
        return $this->generatePDF($reportData);
    }

    /**
     * Generate and download DOCX report.
     */
    public function generateDOCXReport(Request $request)
    {
        $request->validate([
            'date_range' => 'required|string',
            'owner_id' => 'nullable|exists:owners,id',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
            'technician_ids' => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
        ]);

        $user = Auth::user();
        
        // Parse date range
        $dateRange = $this->parseDateRange($request->date_range);
        
        // Build query based on filters
        $query = $this->buildReportQuery($request, $user, $dateRange);
        
        // Get the results
        $requests = $query->get();
        
        // Generate report data
        $reportData = $this->generateReportData($requests, $request, $dateRange);
        
        return $this->generateDOCX($reportData);
    }

    /**
     * Convert single property_id and technician_id to arrays for backward compatibility.
     */
    private function normalizeSingleSelections(Request $request)
    {
        // Convert single property_id to property_ids array
        if ($request->filled('property_id') && !$request->filled('property_ids')) {
            $request->merge(['property_ids' => [$request->property_id]]);
        }
        
        // Convert single technician_id to technician_ids array
        if ($request->filled('technician_id') && !$request->filled('technician_ids')) {
            $request->merge(['technician_ids' => [$request->technician_id]]);
        }
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
        $user = Auth::user();
        
        // Add navigation stats for both mobile and desktop layouts
        if ($user->isAdmin()) {
            $navigationStats = [
                'ownersCount' => \App\Models\Owner::count(),
                'propertiesCount' => \App\Models\Property::count(),
                'techniciansCount' => \App\Models\User::whereHas('role', function($q) {
                    $q->where('slug', 'technician');
                })->count(),
                'requestsCount' => \App\Models\MaintenanceRequest::count(),
            ];
        } else {
            // Property manager stats
            $navigationStats = [
                'ownersCount' => $user->managedOwners()->count(),
                'propertiesCount' => $user->managedProperties()->count(),
                'techniciansCount' => \App\Models\User::whereHas('role', function($q) {
                    $q->where('slug', 'technician');
                })->where('invited_by', $user->id)->count(),
                'requestsCount' => \App\Models\MaintenanceRequest::whereIn('property_id', $user->managedProperties()->pluck('id'))->count(),
            ];
        }
        
        // Merge navigation stats with report data
        $reportData = array_merge($reportData, $navigationStats);
        
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
        // Add owner, properties, and technicians details for PDF report title
        $ownerName = null;
        $propertyNames = [];
        $technicianNames = [];
        
        $request = request(); // Get current request
        
        if ($request->filled('owner_id')) {
            $owner = \App\Models\Owner::find($request->owner_id);
            $ownerName = $owner ? $owner->name : null;
        }
        
        if ($request->filled('property_ids')) {
            $propertyNames = \App\Models\Property::whereIn('id', $request->property_ids)
                ->pluck('name')
                ->toArray();
        }
        
        if ($request->filled('technician_ids')) {
            $technicianNames = \App\Models\User::whereIn('id', $request->technician_ids)
                ->pluck('name')
                ->toArray();
        }
        
        // Generate AI Summary for PDF
        $aiSummary = null;
        try {
            // Reload requests with comments for comprehensive AI analysis
            $user = Auth::user();
            $dateRange = $reportData['dateRange'];
            
            // Build query to get requests with comments
            $query = $this->buildReportQuery($request, $user, $dateRange);
            $requestsWithComments = $query->with(['comments.user', 'property.owner', 'assignedTechnician'])->get();
            
            // Prepare data for AI prompt
            $promptData = $this->prepareDataForAI($requestsWithComments, $reportData, $dateRange);
            
            // Base AI Prompt Template
            $basePrompt = "You are an assistant helping a property manager summarize maintenance reports. Based on the data, and comments inside each task from either the property manager or the technician, create a clear and professional summary. Highlight the most important details such as total number of tasks, completed vs pending, any recurring issues, top-performing technicians, and any unusual delays. Be concise, use plain language, and include relevant stats where helpful.";
            
            // Generate AI summary
            $response = OpenAI::chat()->create([
                'model' => config('openai.default_model', 'gpt-3.5-turbo'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $basePrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $promptData
                    ]
                ],
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            
            $aiSummary = $response->choices[0]->message->content;
            
        } catch (\Exception $e) {
            \Log::error('PDF AI Summary Generation Error: ' . $e->getMessage());
            $aiSummary = 'AI summary could not be generated at this time.';
        }
        
        // Add additional data for PDF
        $reportData['owner_name'] = $ownerName;
        $reportData['property_names'] = $propertyNames;
        $reportData['technician_names'] = $technicianNames;
        $reportData['ai_summary'] = $aiSummary;
        
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

    /**
     * Generate DOCX report.
     */
    private function generateDOCX(array $reportData)
    {
        // Add owner, properties, and technicians details for DOCX report title
        $ownerName = null;
        $propertyNames = [];
        $technicianNames = [];
        
        $request = request(); // Get current request
        
        if ($request->filled('owner_id')) {
            $owner = \App\Models\Owner::find($request->owner_id);
            $ownerName = $owner ? $owner->name : null;
        }
        
        if ($request->filled('property_ids')) {
            $propertyNames = \App\Models\Property::whereIn('id', $request->property_ids)
                ->pluck('name')
                ->toArray();
        }
        
        if ($request->filled('technician_ids')) {
            $technicianNames = \App\Models\User::whereIn('id', $request->technician_ids)
                ->pluck('name')
                ->toArray();
        }
        
        // Generate AI Summary for DOCX
        $aiSummary = null;
        try {
            // Reload requests with comments for comprehensive AI analysis
            $user = Auth::user();
            $dateRange = $reportData['dateRange'];
            
            // Build query to get requests with comments
            $query = $this->buildReportQuery($request, $user, $dateRange);
            $requestsWithComments = $query->with(['comments.user', 'property.owner', 'assignedTechnician'])->get();
            
            // Prepare data for AI prompt
            $promptData = $this->prepareDataForAI($requestsWithComments, $reportData, $dateRange);
            
            // Base AI Prompt Template
            $basePrompt = "You are an assistant helping a property manager summarize maintenance reports. Based on the data, and comments inside each task from either the property manager or the technician, create a clear and professional summary. Highlight the most important details such as total number of tasks, completed vs pending, any recurring issues, top-performing technicians, and any unusual delays. Be concise, use plain language, and include relevant stats where helpful.";
            
            // Generate AI summary
            $response = OpenAI::chat()->create([
                'model' => config('openai.default_model', 'gpt-3.5-turbo'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $basePrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $promptData
                    ]
                ],
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            
            $aiSummary = $response->choices[0]->message->content;
            
        } catch (\Exception $e) {
            \Log::error('DOCX AI Summary Generation Error: ' . $e->getMessage());
            $aiSummary = 'AI summary could not be generated at this time.';
        }
        
        // Add additional data for DOCX
        $reportData['owner_name'] = $ownerName;
        $reportData['property_names'] = $propertyNames;
        $reportData['technician_names'] = $technicianNames;
        $reportData['ai_summary'] = $aiSummary;
        
        // Create new PHPWord instance
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // Add styles
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 20]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 16]);
        $phpWord->addTitleStyle(3, ['bold' => true, 'size' => 14]);
        
        // Create section
        $section = $phpWord->addSection();
        
        // Add report header
        $section->addTitle('📊 Report' . (isset($reportData['owner_name']) && $reportData['owner_name'] ? ' for ' . $reportData['owner_name'] : ''), 1);
        
        // Add filter information
        if ((isset($reportData['property_names']) && count($reportData['property_names']) > 0) || 
            (isset($reportData['technician_names']) && count($reportData['technician_names']) > 0)) {
            
            if (isset($reportData['property_names']) && count($reportData['property_names']) > 0) {
                $section->addText('Properties: ' . implode(', ', $reportData['property_names']), ['size' => 11]);
            }
            
            if (isset($reportData['technician_names']) && count($reportData['technician_names']) > 0) {
                $section->addText('Technicians: ' . implode(', ', $reportData['technician_names']), ['size' => 11]);
            }
        }
        
        // Add date range and generation info
        $section->addText($reportData['dateRange']['label'], ['size' => 11, 'color' => '666666']);
        $section->addText('Generated on ' . now()->format('F j, Y \a\t g:i A'), ['size' => 10, 'color' => '999999']);
        $section->addTextBreak();
        
        // Add AI Summary section
        $section->addTitle('🧠 AI Summary', 2);
        
        if (isset($reportData['ai_summary']) && $reportData['ai_summary']) {
            $section->addText($reportData['ai_summary'], ['size' => 11]);
        } else {
            $section->addText('Maintenance Report Summary:', ['bold' => true, 'size' => 11]);
            $section->addText('- Total maintenance requests: ' . $reportData['summary']['total_requests'], ['size' => 11]);
            $section->addText('- Completed tasks: ' . $reportData['summary']['completed_requests'], ['size' => 11]);
            $section->addText('- Pending tasks: ' . $reportData['summary']['pending_requests'], ['size' => 11]);
            $section->addText('- Average completion time: ' . ($reportData['summary']['average_completion_time'] ? $reportData['summary']['average_completion_time'] . ' hours' : 'N/A'), ['size' => 11]);
            
            if (isset($reportData['breakdowns']['status']) && count($reportData['breakdowns']['status']) > 0) {
                $section->addTextBreak();
                $section->addText('Status Breakdown:', ['bold' => true, 'size' => 11]);
                foreach ($reportData['breakdowns']['status'] as $status => $count) {
                    $section->addText('- ' . ucfirst($status) . ': ' . $count, ['size' => 11]);
                }
            }
            
            if (isset($reportData['breakdowns']['priority']) && count($reportData['breakdowns']['priority']) > 0) {
                $section->addTextBreak();
                $section->addText('Priority Breakdown:', ['bold' => true, 'size' => 11]);
                foreach ($reportData['breakdowns']['priority'] as $priority => $count) {
                    $section->addText('- ' . ucfirst($priority) . ' Priority: ' . $count, ['size' => 11]);
                }
            }
        }
        
        $section->addTextBreak();
        
        // Add status summary table
        $section->addTitle('Status Summary', 2);
        
        $statusTable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $statusTable->addRow();
        $statusTable->addCell(2000)->addText('Declined', ['bold' => true, 'size' => 11]);
        $statusTable->addCell(2000)->addText('Assigned', ['bold' => true, 'size' => 11]);
        $statusTable->addCell(2000)->addText('Accepted', ['bold' => true, 'size' => 11]);
        $statusTable->addCell(2000)->addText('Started', ['bold' => true, 'size' => 11]);
        $statusTable->addCell(2000)->addText('Completed', ['bold' => true, 'size' => 11]);
        
        $statusTable->addRow();
        $statusTable->addCell(2000)->addText($reportData['requests']->where('status', 'declined')->count(), ['bold' => true, 'size' => 14]);
        $statusTable->addCell(2000)->addText($reportData['requests']->where('status', 'assigned')->count(), ['bold' => true, 'size' => 14]);
        $statusTable->addCell(2000)->addText($reportData['requests']->where('status', 'accepted')->count(), ['bold' => true, 'size' => 14]);
        $statusTable->addCell(2000)->addText($reportData['requests']->where('status', 'started')->count(), ['bold' => true, 'size' => 14]);
        $statusTable->addCell(2000)->addText($reportData['requests']->where('status', 'completed')->count(), ['bold' => true, 'size' => 14]);
        
        $section->addTextBreak();
        
        // Add detailed requests table
        if ($reportData['requests']->isNotEmpty()) {
            $section->addTitle('Maintenance Requests', 3);
            
            // Create table
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
            
            // Add table headers
            $table->addRow();
            $table->addCell(3000)->addText('Property', ['bold' => true, 'size' => 11]);
            $table->addCell(1500)->addText('Priority', ['bold' => true, 'size' => 11]);
            $table->addCell(1500)->addText('Date', ['bold' => true, 'size' => 11]);
            $table->addCell(1500)->addText('Status', ['bold' => true, 'size' => 11]);
            
            // Add table data
            foreach ($reportData['requests'] as $request) {
                $table->addRow();
                
                // Property cell with name and address
                $propertyCell = $table->addCell(3000);
                $propertyCell->addText($request->property->name ?? 'N/A', ['bold' => true, 'size' => 11]);
                $propertyCell->addText($request->property->address ?? 'No address', ['size' => 10, 'color' => '666666']);
                
                // Priority cell
                $priorityCell = $table->addCell(1500);
                $priorityCell->addText(ucfirst($request->priority), ['size' => 11]);
                
                // Date cell
                $dateCell = $table->addCell(1500);
                $dateCell->addText($request->created_at->format('d M, Y'), ['size' => 11]);
                $dateCell->addText($request->created_at->format('H:i'), ['size' => 10, 'color' => '666666']);
                
                // Status cell
                $table->addCell(1500)->addText(ucfirst($request->status), ['size' => 11]);
            }
        } else {
            $section->addTitle('Maintenance Requests', 3);
            $section->addText('No requests found', ['size' => 11]);
            $section->addText('No maintenance requests match the selected criteria.', ['size' => 10, 'color' => '666666']);
        }
        
        $section->addTextBreak();
        
        // Add footer
        $section->addText('This report was generated automatically by MaintainXtra', ['size' => 10, 'color' => '666666'], ['alignment' => 'center']);
        
        // Generate filename
        $filename = 'maintenance_report_' . now()->format('Y-m-d_H-i-s') . '.docx';
        
        // Create writer
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
        $writer->save($tempFile);
        
        // Return file as download
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend();
    }

    /**
     * Generate AI Summary for the report.
     */
    public function generateAISummary(Request $request)
    {
        $user = Auth::user();
        
        // Only allow property managers and admins
        if (!$user->isPropertyManager() && !$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Parse date range
            $dateRange = $this->parseDateRange($request->date_range, $request->start_date, $request->end_date);
            
            // Build query and get requests with comments
            $query = $this->buildReportQuery($request, $user, $dateRange);
            $requests = $query->with(['comments.user', 'property.owner', 'assignedTechnician'])->get();
            
            // Generate comprehensive report data
            $reportData = $this->generateReportData($requests, $request, $dateRange);
            
            // Prepare data for AI prompt
            $promptData = $this->prepareDataForAI($requests, $reportData, $dateRange);
            
            // Base AI Prompt Template
            $basePrompt = "You are an assistant helping a property manager summarize maintenance reports. Based on the data, and comments inside each task from either the property manager or the technician, create a clear and professional summary. Highlight the most important details such as total number of tasks, completed vs pending, any recurring issues, top-performing technicians, and any unusual delays. Be concise, use plain language, and include relevant stats where helpful.";
            
            // Generate AI summary
            $response = OpenAI::chat()->create([
                'model' => config('openai.default_model', 'gpt-3.5-turbo'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $basePrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $promptData
                    ]
                ],
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            
            $aiSummary = $response->choices[0]->message->content;
            
            return response()->json([
                'success' => true,
                'summary' => $aiSummary
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AI Summary Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate AI summary. Please try again later.'
            ], 500);
        }
    }

    /**
     * Prepare data for AI analysis.
     */
    private function prepareDataForAI($requests, $reportData, $dateRange)
    {
        $data = [];
        
        // Report overview
        $data[] = "=== MAINTENANCE REPORT SUMMARY ===";
        $data[] = "Report Period: {$dateRange['label']}";
        $data[] = "Total Requests: {$reportData['summary']['total_requests']}";
        $data[] = "Completed: {$reportData['summary']['completed_requests']}";
        $data[] = "Pending: {$reportData['summary']['pending_requests']}";
        $data[] = "Average Completion Time: {$reportData['summary']['average_completion_time']} hours";
        $data[] = "";
        
        // Status breakdown
        if (!empty($reportData['breakdowns']['status'])) {
            $data[] = "=== STATUS BREAKDOWN ===";
            foreach ($reportData['breakdowns']['status'] as $status => $count) {
                $data[] = ucfirst($status) . ": {$count}";
            }
            $data[] = "";
        }
        
        // Priority breakdown
        if (!empty($reportData['breakdowns']['priority'])) {
            $data[] = "=== PRIORITY BREAKDOWN ===";
            foreach ($reportData['breakdowns']['priority'] as $priority => $count) {
                $data[] = ucfirst($priority) . " Priority: {$count}";
            }
            $data[] = "";
        }
        
        // Individual requests with comments
        $data[] = "=== INDIVIDUAL MAINTENANCE REQUESTS ===";
        foreach ($requests as $request) {
            $data[] = "Request #{$request->id}: {$request->title}";
            $ownerName = $request->property->owner->name ?? 'N/A';
            $data[] = "Property: {$request->property->name} (Owner: {$ownerName})";
            $data[] = "Priority: {$request->priority} | Status: {$request->status}";
            $technicianName = $request->assignedTechnician->name ?? 'Not Assigned';
            $data[] = "Technician: {$technicianName}";
            $data[] = "Created: {$request->created_at->format('Y-m-d H:i')}";
            
            if ($request->completed_at) {
                $completionTime = $request->created_at->diffInHours($request->completed_at);
                $data[] = "Completed: {$request->completed_at->format('Y-m-d H:i')} (Duration: {$completionTime} hours)";
            }
            
            // Add comments
            if ($request->comments && $request->comments->count() > 0) {
                $data[] = "Comments:";
                foreach ($request->comments as $comment) {
                    $userRole = $comment->user->role->name ?? 'User';
                    $userName = $comment->user->name;
                    $commentText = $comment->comment;
                    $data[] = "  - {$userRole} ({$userName}): {$commentText}";
                }
            }
            
            $data[] = "---";
        }
        
        return implode("\n", $data);
    }
} 