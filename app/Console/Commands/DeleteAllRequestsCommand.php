<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Storage;

class DeleteAllRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:delete-all {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all maintenance requests from the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = MaintenanceRequest::count();
        
        if ($count === 0) {
            $this->info('No maintenance requests found in the system.');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("This will delete ALL {$count} maintenance requests. This action cannot be undone. Are you sure?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info("Deleting {$count} maintenance requests...");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        // Delete in chunks to avoid memory issues
        MaintenanceRequest::chunk(100, function($requests) use ($bar) {
            foreach($requests as $request) {
                // Delete associated images from storage
                foreach($request->images as $image) {
                    try {
                        Storage::delete('public/' . $image->image_path);
                    } catch (\Exception $e) {
                        $this->warn("Could not delete image: {$image->image_path}");
                    }
                }
                
                // Delete the request (this will cascade delete related records)
                $request->delete();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Successfully deleted {$count} maintenance requests.");
        
        return 0;
    }
} 