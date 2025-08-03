<?php

namespace App\Console\Commands;

use App\Models\Owner;
use App\Models\Property;
use Illuminate\Console\Command;

class ReassignOwnerPropertiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'owners:reassign-properties {from-owner : The ID of the owner to reassign properties from} {to-owner : The ID of the owner to reassign properties to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reassign all properties from one owner to another';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fromOwnerId = $this->argument('from-owner');
        $toOwnerId = $this->argument('to-owner');

        // Find the owners
        $fromOwner = Owner::find($fromOwnerId);
        $toOwner = Owner::find($toOwnerId);

        if (!$fromOwner) {
            $this->error("Owner with ID {$fromOwnerId} not found.");
            return 1;
        }

        if (!$toOwner) {
            $this->error("Owner with ID {$toOwnerId} not found.");
            return 1;
        }

        // Check if from-owner has properties
        $properties = $fromOwner->properties;
        
        if ($properties->count() === 0) {
            $this->info("Owner '{$fromOwner->name}' has no properties to reassign.");
            return 0;
        }

        // Confirm the action
        $this->info("Found {$properties->count()} properties owned by '{$fromOwner->name}':");
        foreach ($properties as $property) {
            $this->line("- {$property->name} ({$property->address})");
        }

        if (!$this->confirm("Do you want to reassign these properties to '{$toOwner->name}'?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Reassign properties
        $updatedCount = Property::where('owner_id', $fromOwnerId)
            ->update(['owner_id' => $toOwnerId]);

        $this->info("Successfully reassigned {$updatedCount} properties from '{$fromOwner->name}' to '{$toOwner->name}'.");

        // Show updated property count for both owners
        $fromOwnerCount = Owner::find($fromOwnerId)->properties()->count();
        $toOwnerCount = Owner::find($toOwnerId)->properties()->count();

        $this->info("Owner '{$fromOwner->name}' now has {$fromOwnerCount} properties.");
        $this->info("Owner '{$toOwner->name}' now has {$toOwnerCount} properties.");

        return 0;
    }
} 