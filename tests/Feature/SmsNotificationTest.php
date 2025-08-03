<?php

namespace Tests\Feature;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SmsNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable SMS sending for tests
        Config::set('twilio.sms.enabled', false);
    }

    /** @test */
    public function it_sends_sms_notification_when_technician_is_assigned()
    {
        // Create a property manager
        $manager = User::factory()->create([
            'role_id' => 2, // property_manager role
        ]);

        // Create a technician with phone number
        $technician = User::factory()->create([
            'role_id' => 4, // technician role
            'phone' => '+1234567890',
            'invited_by' => $manager->id,
        ]);

        // Create a property
        $property = Property::factory()->create([
            'manager_id' => $manager->id,
        ]);

        // Create a maintenance request
        $maintenance_request = MaintenanceRequest::factory()->create([
            'property_id' => $property->id,
            'title' => 'Test Maintenance Request',
            'description' => 'Test description',
            'priority' => 'medium',
            'status' => 'accepted',
        ]);

        // Mock the SMS service
        $sms_service = $this->mock(SmsService::class);
        $sms_service->shouldReceive('sendTechnicianAssignmentNotification')
            ->once()
            ->with($maintenance_request, $technician)
            ->andReturn(true);

        $this->app->instance(SmsService::class, $sms_service);

        // Assign technician to the request
        $response = $this->actingAs($manager)
            ->post(route('maintenance.assign', $maintenance_request), [
                'assigned_to' => $technician->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_does_not_send_sms_when_technician_has_no_phone()
    {
        // Create a property manager
        $manager = User::factory()->create([
            'role_id' => 2, // property_manager role
        ]);

        // Create a technician without phone number
        $technician = User::factory()->create([
            'role_id' => 4, // technician role
            'phone' => null,
            'invited_by' => $manager->id,
        ]);

        // Create a property
        $property = Property::factory()->create([
            'manager_id' => $manager->id,
        ]);

        // Create a maintenance request
        $maintenance_request = MaintenanceRequest::factory()->create([
            'property_id' => $property->id,
            'status' => 'accepted',
        ]);

        // Mock the SMS service
        $sms_service = $this->mock(SmsService::class);
        $sms_service->shouldReceive('sendTechnicianAssignmentNotification')
            ->once()
            ->with($maintenance_request, $technician)
            ->andReturn(false);

        $this->app->instance(SmsService::class, $sms_service);

        // Assign technician to the request
        $response = $this->actingAs($manager)
            ->post(route('maintenance.assign', $maintenance_request), [
                'assigned_to' => $technician->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_validates_phone_number_format()
    {
        $sms_service = app(SmsService::class);

        // Valid phone numbers
        $this->assertTrue($sms_service->isValidPhoneNumber('+1234567890'));
        $this->assertTrue($sms_service->isValidPhoneNumber('+447911123456'));

        // Invalid phone numbers
        $this->assertFalse($sms_service->isValidPhoneNumber('1234567890'));
        $this->assertFalse($sms_service->isValidPhoneNumber('(123) 456-7890'));
        $this->assertFalse($sms_service->isValidPhoneNumber('invalid'));
    }

    /** @test */
    public function it_formats_phone_numbers_correctly()
    {
        $sms_service = app(SmsService::class);

        // Already formatted
        $this->assertEquals('+1234567890', $sms_service->formatPhoneNumber('+1234567890'));

        // US number without country code
        $this->assertEquals('+11234567890', $sms_service->formatPhoneNumber('1234567890'));

        // Remove formatting
        $this->assertEquals('+11234567890', $sms_service->formatPhoneNumber('(123) 456-7890'));
    }
} 