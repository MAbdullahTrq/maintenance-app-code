<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\RequestComment;
use App\Models\RequestImage;
use App\Models\Subscription;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CleanDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean database keeping only admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete ALL data except the admin account. Are you sure?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('Starting database cleanup...');

        try {
            DB::beginTransaction();

            // Find admin user
            $admin = User::whereHas('role', function ($query) {
                $query->where('slug', 'admin');
            })->first();

            if (!$admin) {
                $this->error('No admin user found! Aborting cleanup.');
                return;
            }

            $this->info("Admin user found: {$admin->name} ({$admin->email})");

            // Delete maintenance request images from storage
            $this->info('Cleaning up uploaded images...');
            $images = RequestImage::all();
            foreach ($images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }

            // Delete user profile images (except admin)
            $users = User::where('id', '!=', $admin->id)->whereNotNull('image')->get();
            foreach ($users as $user) {
                if ($user->image && Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }
            }

            // Delete property images
            $properties = Property::whereNotNull('image')->get();
            foreach ($properties as $property) {
                if (Storage::disk('public')->exists($property->image)) {
                    Storage::disk('public')->delete($property->image);
                }
            }

            // Delete database records in proper order (respecting foreign key constraints)
            $this->info('Deleting database records...');

            // Delete request images
            RequestImage::truncate();
            $this->info('✓ Request images deleted');

            // Delete request comments
            RequestComment::truncate();
            $this->info('✓ Request comments deleted');

            // Delete maintenance requests
            MaintenanceRequest::truncate();
            $this->info('✓ Maintenance requests deleted');

            // Delete subscriptions
            Subscription::truncate();
            $this->info('✓ Subscriptions deleted');

            // Delete properties
            Property::truncate();
            $this->info('✓ Properties deleted');

            // Delete all users except admin
            $deletedUsersCount = User::where('id', '!=', $admin->id)->count();
            User::where('id', '!=', $admin->id)->delete();
            $this->info("✓ {$deletedUsersCount} non-admin users deleted");

            // Clear any permission/role assignments for deleted users
            DB::table('model_has_permissions')->whereNotIn('model_id', [$admin->id])->delete();
            DB::table('model_has_roles')->whereNotIn('model_id', [$admin->id])->delete();

            // Reset auto-increment IDs (optional)
            $this->info('Resetting auto-increment counters...');
            $tables = [
                'maintenance_requests',
                'properties', 
                'request_images',
                'request_comments',
                'subscriptions'
            ];

            foreach ($tables as $table) {
                DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            }

            DB::commit();

            $this->info('');
            $this->info('🎉 Database cleanup completed successfully!');
            $this->info('');
            $this->info('Remaining data:');
            $this->info("- Admin user: {$admin->name} ({$admin->email})");
            $this->info('- Roles and subscription plans (preserved)');
            $this->info('');
            $this->warn('Note: You may want to clear any cached data or sessions.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error during cleanup: ' . $e->getMessage());
            $this->error('Database rollback performed. No changes were made.');
        }
    }
} 