<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Court;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VenueOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('email', 'owner@sporthive.id')->first();
        $player = User::where('email', 'player@sporthive.id')->first();

        if (! $owner || ! $player) {
            return;
        }

        $venue = Venue::where('owner_id', $owner->id)->where('validation_status', 'APPROVED')->first();

        if (! $venue) {
            return;
        }

        $futsalCourt = Court::where('venue_id', $venue->id)->where('name', 'like', '%Futsal%')->first();
        $badmintonCourt = Court::where('venue_id', $venue->id)->where('name', 'like', '%Badminton%')->first();

        $today = Carbon::today();

        // 1. Buat Time Slots Harian untuk Lapangan Futsal
        if ($futsalCourt) {
            $hours = [
                ['start' => '08:00', 'end' => '09:00', 'price' => 120000],
                ['start' => '09:00', 'end' => '10:00', 'price' => 120000],
                ['start' => '10:00', 'end' => '11:00', 'price' => 150000, 'book' => 'PAID', 'code' => 'SH-FT01'],
                ['start' => '13:00', 'end' => '14:00', 'price' => 150000],
                ['start' => '14:00', 'end' => '15:00', 'price' => 150000, 'book' => 'PENDING', 'code' => 'SH-FT02'],
                ['start' => '18:00', 'end' => '19:00', 'price' => 180000],
                ['start' => '19:00', 'end' => '20:00', 'price' => 180000, 'status' => 'OFF'],
                ['start' => '20:00', 'end' => '21:00', 'price' => 180000],
            ];

            foreach ($hours as $h) {
                $startAt = Carbon::parse("{$today->toDateString()} {$h['start']}");
                $endAt = Carbon::parse("{$today->toDateString()} {$h['end']}");

                $slotStatus = $h['status'] ?? (isset($h['book']) ? 'BOOKED' : 'AVAILABLE');

                $slot = TimeSlot::query()->firstOrCreate(
                    [
                        'court_id' => $futsalCourt->id,
                        'start_at' => $startAt,
                        'end_at' => $endAt,
                    ],
                    [
                        'price' => $h['price'],
                        'status' => $slotStatus,
                    ]
                );

                if (isset($h['book'])) {
                    Booking::query()->firstOrCreate(
                        ['slot_id' => $slot->id],
                        [
                            'user_id' => $player->id,
                            'status' => $h['book'],
                            'total_price' => $h['price'],
                            'e_ticket_code' => $h['code'],
                            'payment_ref' => 'PAY-'.strtoupper(uniqid()),
                            'paid_at' => ($h['book'] === 'PAID') ? now() : null,
                        ]
                    );
                }
            }
        }

        // 2. Buat Time Slots Harian untuk Lapangan Badminton
        if ($badmintonCourt) {
            $badmintonHours = [
                ['start' => '07:00', 'end' => '08:00', 'price' => 50000, 'book' => 'COMPLETED', 'code' => 'SH-BD00'],
                ['start' => '08:00', 'end' => '09:00', 'price' => 50000],
                ['start' => '09:00', 'end' => '10:00', 'price' => 50000],
                ['start' => '16:00', 'end' => '17:00', 'price' => 65000],
                ['start' => '17:00', 'end' => '18:00', 'price' => 65000],
                ['start' => '19:00', 'end' => '20:00', 'price' => 75000, 'book' => 'PAID', 'code' => 'SH-BD01'],
                ['start' => '20:00', 'end' => '21:00', 'price' => 75000],
            ];

            foreach ($badmintonHours as $h) {
                $startAt = Carbon::parse("{$today->toDateString()} {$h['start']}");
                $endAt = Carbon::parse("{$today->toDateString()} {$h['end']}");

                $slotStatus = isset($h['book']) ? 'BOOKED' : 'AVAILABLE';

                $slot = TimeSlot::query()->firstOrCreate(
                    [
                        'court_id' => $badmintonCourt->id,
                        'start_at' => $startAt,
                        'end_at' => $endAt,
                    ],
                    [
                        'price' => $h['price'],
                        'status' => $slotStatus,
                    ]
                );

                if (isset($h['book'])) {
                    Booking::query()->firstOrCreate(
                        ['slot_id' => $slot->id],
                        [
                            'user_id' => $player->id,
                            'status' => $h['book'],
                            'total_price' => $h['price'],
                            'e_ticket_code' => $h['code'],
                            'payment_ref' => 'PAY-'.strtoupper(uniqid()),
                            'paid_at' => in_array($h['book'], ['PAID', 'COMPLETED']) ? now()->subHours(2) : null,
                        ]
                    );
                }
            }
        }
    }
}

