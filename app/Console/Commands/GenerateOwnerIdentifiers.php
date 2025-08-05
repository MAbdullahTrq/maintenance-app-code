<?php

namespace App\Console\Commands;

use App\Models\Owner;
use Illuminate\Console\Command;

class GenerateOwnerIdentifiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'owners:generate-identifiers {--regenerate : Regenerate identifiers for all owners}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique identifiers for owners and update their QR codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting owner identifier generation...');

        $owners = Owner::all();
        $count = 0;
        $regenerated = 0;

        foreach ($owners as $owner) {
            $hadIdentifier = !empty($owner->unique_identifier);
            
            // Generate unique identifier
            $owner->ensureUniqueIdentifier();
            
            if (!$hadIdentifier) {
                $count++;
                $this->line("Generated identifier for owner {$owner->name}: {$owner->unique_identifier}");
            } elseif ($this->option('regenerate')) {
                $regenerated++;
                $this->line("Regenerated identifier for owner {$owner->name}: {$owner->unique_identifier}");
            }

            // Generate new QR code with the new URL
            try {
                $qrCodePath = $owner->generateQrCode();
                $this->line("Generated QR code for {$owner->name}: {$qrCodePath}");
            } catch (\Exception $e) {
                $this->error("Failed to generate QR code for {$owner->name}: " . $e->getMessage());
            }
        }

        $this->info("Completed! Generated {$count} new identifiers and regenerated {$regenerated} existing ones.");
        $this->info("All QR codes have been updated with the new generic URLs.");
    }
}
