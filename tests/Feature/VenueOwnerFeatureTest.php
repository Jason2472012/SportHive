<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Court;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Venue;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueOwnerFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_cannot_access_venue_owner_dashboard(): void
    {
        $response = $this->get('/venueowner/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_player_cannot_access_venue_owner_dashboard(): void
    {
        $player = User::where('role', 'PLAYER')->first();
        if ($player) {
            $response = $this->actingAs($player)->get('/venueowner/dashboard');
            $response->assertStatus(403);
        }
    }

    public function test_venue_owner_can_access_all_modules(): void
    {
        $owner = User::where('role', 'VENUE_OWNER')->first();
        $this->assertNotNull($owner);

        // Dashboard
        $this->actingAs($owner)->get('/venueowner/dashboard')->assertStatus(200);

        // Profil Venue
        $this->actingAs($owner)->get('/venueowner/venues')->assertStatus(200);
        $this->actingAs($owner)->get('/venueowner/venues/create')->assertStatus(200);

        // Lapangan & Olahraga
        $this->actingAs($owner)->get('/venueowner/courts')->assertStatus(200);
        $this->actingAs($owner)->get('/venueowner/courts/create')->assertStatus(200);

        // Slot Waktu & Tarif
        $this->actingAs($owner)->get('/venueowner/slots')->assertStatus(200);
        $this->actingAs($owner)->get('/venueowner/slots/create')->assertStatus(200);

        // Reservasi Masuk & E-Tiket
        $this->actingAs($owner)->get('/venueowner/bookings')->assertStatus(200);

        $booking = Booking::first();
        if ($booking) {
            $this->actingAs($owner)->get("/venueowner/bookings/{$booking->id}")->assertStatus(200);
        }
    }

    public function test_venue_owner_can_toggle_slot_status(): void
    {
        $owner = User::where('role', 'VENUE_OWNER')->first();
        $slot = TimeSlot::where('status', '!=', 'BOOKED')->first();

        if ($slot && $owner) {
            $initialStatus = $slot->status;
            $response = $this->actingAs($owner)->patch("/venueowner/slots/{$slot->id}/toggle-status");
            $response->assertSessionHas('success');

            $slot->refresh();
            $expected = ($initialStatus === 'AVAILABLE') ? 'OFF' : 'AVAILABLE';
            $this->assertEquals($expected, $slot->status);
        }
    }

    public function test_venue_owner_can_checkin_booking(): void
    {
        $owner = User::where('role', 'VENUE_OWNER')->first();
        $booking = Booking::where('status', 'PAID')->first();

        if ($booking && $owner) {
            $response = $this->actingAs($owner)->patch("/venueowner/bookings/{$booking->id}/check-in");
            $response->assertSessionHas('success');

            $booking->refresh();
            $this->assertEquals('COMPLETED', $booking->status);
        }
    }
}

