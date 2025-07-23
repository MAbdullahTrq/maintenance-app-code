<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert any users with team_member role to editor role
        $teamMemberRole = Role::where('slug', 'team_member')->first();
        $editorRole = Role::where('slug', 'editor')->first();
        
        if ($teamMemberRole && $editorRole) {
            User::where('role_id', $teamMemberRole->id)->update(['role_id' => $editorRole->id]);
        }
        
        // Then delete the team_member role
        if ($teamMemberRole) {
            $teamMemberRole->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the team_member role
        $teamMemberRole = Role::create([
            'name' => 'Team Member',
            'slug' => 'team_member',
            'description' => 'Basic team member with limited access to view and manage properties.',
        ]);
        
        // Convert editor users back to team_member (this is a simplified rollback)
        $editorRole = Role::where('slug', 'editor')->first();
        if ($editorRole && $teamMemberRole) {
            User::where('role_id', $editorRole->id)->update(['role_id' => $teamMemberRole->id]);
        }
    }
};
