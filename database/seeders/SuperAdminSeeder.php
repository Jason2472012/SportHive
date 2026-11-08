<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Court;
use App\Models\Report;
use App\Models\Sport;
use App\Models\SportSkillLevel;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Default Super Admin
        $superAdmin = User::query()->firstOrNew([
            'email' => 'admin@example.com',
        ]);
        $superAdmin->forceFill([
            'name' => 'Super Admin',
            'role' => 'SUPER_ADMIN',
            'password_hash' => Hash::make('password'),
            'phone' => '081100001111',
            'is_active' => true,
        ])->save();

        // 2. Sample Akun Mitra Venue Owner & Pemain
        $venueOwner = User::query()->firstOrNew([
            'email' => 'owner@sporthive.id',
        ]);
        $venueOwner->forceFill([
            'name' => 'Mitra Arena Bandung',
            'role' => 'VENUE_OWNER',
            'password_hash' => Hash::make('password'),
            'phone' => '081222333444',
            'is_active' => true,
        ])->save();

        $player = User::query()->firstOrNew([
            'email' => 'player@sporthive.id',
        ]);
        $player->forceFill([
            'name' => 'Andi Pratama',
            'role' => 'PLAYER',
            'password_hash' => Hash::make('password'),
            'phone' => '081333444555',
            'rating_avg' => 4.75,
            'rating_count' => 4,
            'is_active' => true,
        ])->save();

        // 3. Master Data Cabang Olahraga & Daftar Skill Level (Sesuai Dokumen Word)
        $sportsList = ['Futsal', 'Badminton', 'Basket', 'Tenis', 'Mini Soccer'];
        $skillLevels = [
            ['name' => 'Beginner', 'level_order' => 1],
            ['name' => 'Intermediate', 'level_order' => 2],
            ['name' => 'Advanced', 'level_order' => 3],
        ];

        $futsalSport = null;
        $badmintonSport = null;

        foreach ($sportsList as $sportName) {
            $sport = Sport::query()->firstOrCreate(
                ['name' => $sportName],
                ['is_active' => true, 'created_at' => now()]
            );

            if ($sportName === 'Futsal') {
                $futsalSport = $sport;
            } elseif ($sportName === 'Badminton') {
                $badmintonSport = $sport;
            }

            foreach ($skillLevels as $level) {
                SportSkillLevel::query()->firstOrCreate(
                    ['sport_id' => $sport->id, 'name' => $level['name']],
                    ['level_order' => $level['level_order']]
                );
            }
        }

        // 4. Sample Data Venue (1 Approved & 1 Pending Validasi Mitra)
        $approvedVenue = Venue::query()->firstOrCreate(
            ['name' => 'SportHive Arena Pasteur', 'owner_id' => $venueOwner->id],
            [
                'city' => 'Bandung',
                'address' => 'Jl. Dr. Djunjunan No. 88, Pasteur, Bandung',
                'description' => 'Fasilitas lapangan Futsal sintetis dan Badminton indoor berstandar turnamen.',
                'validation_status' => 'APPROVED',
                'validated_by' => $superAdmin->id,
                'validated_at' => now(),
            ]
        );

        Venue::query()->firstOrCreate(
            ['name' => 'GOR Bintang Sport Center', 'owner_id' => $venueOwner->id],
            [
                'city' => 'Bandung',
                'address' => 'Jl. Surya Sumantri No. 45, Sukajadi, Bandung',
                'description' => 'Pengajuan mitra baru: 2 lapangan Futsal dan 4 lapangan Badminton (Menunggu verifikasi berkas legalitas).',
                'validation_status' => 'PENDING',
            ]
        );

        if ($futsalSport) {
            Court::query()->firstOrCreate(
                ['venue_id' => $approvedVenue->id, 'name' => 'Lapangan Futsal A'],
                ['sport_id' => $futsalSport->id, 'is_active' => true, 'created_at' => now()]
            );
        }

        if ($badmintonSport) {
            Court::query()->firstOrCreate(
                ['venue_id' => $approvedVenue->id, 'name' => 'Lapangan Badminton 1'],
                ['sport_id' => $badmintonSport->id, 'is_active' => true, 'created_at' => now()]
            );
        }

        // 5. Sample Laporan & Feedback
        if (Report::query()->count() === 0) {
            Report::query()->create([
                'reporter_id' => $player->id,
                'target_type' => 'VENUE',
                'target_id' => $approvedVenue->id,
                'reason' => 'Lampu di bagian sudut Lapangan Badminton 1 agak redup saat jadwal malam pukul 20.00 WIB, mohon dicek kembali.',
                'status' => 'OPEN',
                'created_at' => now(),
            ]);
        }

        // 6. Sample Log Aktivitas Sistem
        if (ActivityLog::query()->count() === 0) {
            ActivityLog::recordLog(
                $superAdmin->id,
                'SYSTEM_SEED',
                'Inisialisasi data awal Super Admin, Master Olahraga, Skill Level, dan sampel pengajuan Mitra Venue.',
                '127.0.0.1'
            );
        }
    }
}
