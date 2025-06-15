<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Mail\TechnicianWelcomeMail;

class TechnicianWelcomeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_email_sent_when_technician_created_via_mobile()
    {
        Mail::fake();

        // Create a property manager
        $managerRole = Role::factory()->create(['name' => 'Property Manager', 'slug' => 'property_manager']);
        $manager = User::factory()->create(['role_id' => $managerRole->id]);

        // Create technician role
        $technicianRole = Role::factory()->create(['name' => 'Technician', 'slug' => 'technician']);

        // Act as the manager
        $this->actingAs($manager);

        // Create a technician via mobile interface
        $response = $this->post(route('mobile.technicians.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);

        // Assert redirect
        $response->assertRedirect(route('mobile.technicians.index'));

        // Assert technician was created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'invited_by' => $manager->id,
        ]);

        // Assert welcome email was sent
        Mail::assertSent(TechnicianWelcomeMail::class, function ($mail) {
            return $mail->technician->email === 'john@example.com';
        });

        // Assert verification token was generated
        $technician = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($technician->verification_token);
        $this->assertNotNull($technician->verification_token_expires_at);
    }

    public function test_verification_token_is_valid_for_24_hours()
    {
        $user = User::factory()->create();
        
        $token = $user->generateVerificationToken();
        
        // Token should be valid immediately
        $this->assertTrue($user->isValidVerificationToken($token));
        
        // Simulate token expiry
        $user->update(['verification_token_expires_at' => now()->subHour()]);
        
        // Token should be invalid after expiry
        $this->assertFalse($user->isValidVerificationToken($token));
    }

    public function test_verification_token_can_be_cleared()
    {
        $user = User::factory()->create();
        
        $token = $user->generateVerificationToken();
        $this->assertNotNull($user->verification_token);
        
        $user->clearVerificationToken();
        $user->refresh();
        
        $this->assertNull($user->verification_token);
        $this->assertNull($user->verification_token_expires_at);
    }
}
