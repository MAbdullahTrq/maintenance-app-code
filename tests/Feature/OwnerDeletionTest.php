<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_prevents_deletion_of_owner_with_properties()
    {
        // Create a property manager
        $manager = User::factory()->create([
            'role_id' => 2, // property_manager role
        ]);

        // Create an owner
        $owner = Owner::factory()->create([
            'manager_id' => $manager->id,
        ]);

        // Create a property owned by this owner
        $property = Property::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $owner->id,
        ]);

        // Try to delete the owner via web interface
        $response = $this->actingAs($manager)
            ->delete(route('owners.destroy', $owner));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('cannot be deleted because', session('error'));
        $this->assertStringContainsString($property->name, session('error'));

        // Verify owner still exists
        $this->assertDatabaseHas('owners', ['id' => $owner->id]);
        $this->assertDatabaseHas('properties', ['id' => $property->id]);
    }

    /** @test */
    public function it_prevents_deletion_of_owner_with_properties_via_mobile()
    {
        // Create a property manager
        $manager = User::factory()->create([
            'role_id' => 2, // property_manager role
        ]);

        // Create an owner
        $owner = Owner::factory()->create([
            'manager_id' => $manager->id,
        ]);

        // Create a property owned by this owner
        $property = Property::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $owner->id,
        ]);

        // Try to delete the owner via mobile interface
        $response = $this->actingAs($manager)
            ->delete('/m/ao/' . $owner->id);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('cannot be deleted because', session('error'));
        $this->assertStringContainsString($property->name, session('error'));

        // Verify owner still exists
        $this->assertDatabaseHas('owners', ['id' => $owner->id]);
        $this->assertDatabaseHas('properties', ['id' => $property->id]);
    }

    /** @test */
    public function it_allows_deletion_of_owner_without_properties()
    {
        // Create a property manager
        $manager = User::factory()->create([
            'role_id' => 2, // property_manager role
        ]);

        // Create an owner without properties
        $owner = Owner::factory()->create([
            'manager_id' => $manager->id,
        ]);

        // Delete the owner via web interface
        $response = $this->actingAs($manager)
            ->delete(route('owners.destroy', $owner));

        $response->assertRedirect(route('owners.index'));
        $response->assertSessionHas('success');

        // Verify owner is deleted
        $this->assertDatabaseMissing('owners', ['id' => $owner->id]);
    }

    /** @test */
    public function it_allows_deletion_of_owner_without_properties_via_mobile()
    {
        // Create a property manager
        $manager = User::factory()->create([
            'role_id' => 2, // property_manager role
        ]);

        // Create an owner without properties
        $owner = Owner::factory()->create([
            'manager_id' => $manager->id,
        ]);

        // Delete the owner via mobile interface
        $response = $this->actingAs($manager)
            ->delete('/m/ao/' . $owner->id);

        $response->assertRedirect('/m/ao');
        $response->assertSessionHas('success');

        // Verify owner is deleted
        $this->assertDatabaseMissing('owners', ['id' => $owner->id]);
    }

    /** @test */
    public function it_prevents_deletion_at_model_level()
    {
        // Create a property manager
        $manager = User::factory()->create([
            'role_id' => 2, // property_manager role
        ]);

        // Create an owner
        $owner = Owner::factory()->create([
            'manager_id' => $manager->id,
        ]);

        // Create a property owned by this owner
        Property::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $owner->id,
        ]);

        // Try to delete the owner directly (bypassing controller)
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete owner with associated properties');

        $owner->delete();
    }
} 