<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Harvest\Models\Harvest;
use App\Models\User;

class HarvestSeeder extends Seeder
{
    public function run(): void
    {
        $farmerAgus = User::where('email', 'agus@lembangfresh.test')->first();
        $farmerSiti = User::where('email', 'siti@lembangfresh.test')->first();

        $agusId = $farmerAgus ? $farmerAgus->id : 2;
        $sitiId = $farmerSiti ? $farmerSiti->id : 3;

        $items = [
            [
                'farmer_id' => $agusId,
                'commodity' => 'Wortel Brastagi Lembang',
                'variety' => 'Brastagi Jumbo',
                'quantity' => 500,
                'price_per_kg' => 4500,
                'grade' => 'B',
                'marketing_channel' => Harvest::determineMarketingChannel('B'),
                'harvest_date' => now()->subDays(2)->format('Y-m-d'),
                'ready_date' => now()->addDays(1)->format('Y-m-d'),
                'photo_path' => 'harvests/wortel.png',
                'description' => 'Wortel segar hasil kebun Cikidang Lembang. Bentuk tidak 100% seragam namun rasa sangat manis, renyah, dan cocok untuk jus maupun konsumsi UMKM katering.',
                'status' => 'tersedia',
            ],
            [
                'farmer_id' => $agusId,
                'commodity' => 'Tomat Beef & Cherry Lembang',
                'variety' => 'Beef Super',
                'quantity' => 350,
                'price_per_kg' => 8000,
                'grade' => 'A',
                'marketing_channel' => Harvest::determineMarketingChannel('A'),
                'harvest_date' => now()->subDay()->format('Y-m-d'),
                'ready_date' => now()->addDays(2)->format('Y-m-d'),
                'photo_path' => 'harvests/tomat.png',
                'description' => 'Tomat kualitas Grade A dengan tingkat kematangan seragam 85%, kulit mulus tanpa cacat fisik, siap untuk suplai hotel, restoran, dan supermarket.',
                'status' => 'tersedia',
            ],
            [
                'farmer_id' => $sitiId,
                'commodity' => 'Kentang Granola Lembang',
                'variety' => 'Granola Cibodas',
                'quantity' => 1200,
                'price_per_kg' => 12000,
                'grade' => 'A',
                'marketing_channel' => Harvest::determineMarketingChannel('A'),
                'harvest_date' => now()->subDays(3)->format('Y-m-d'),
                'ready_date' => now()->addDays(3)->format('Y-m-d'),
                'photo_path' => 'harvests/kentang.png',
                'description' => 'Kentang dataran tinggi Cibodas Lembang ukuran seragam (Grade A), kadar air rendah, sangat cocok untuk perkedel, olahan resto, dan pasar modern.',
                'status' => 'tersedia',
            ],
            [
                'farmer_id' => $agusId,
                'commodity' => 'Brokoli Hijau Super',
                'variety' => 'Green Magic',
                'quantity' => 250,
                'price_per_kg' => 15000,
                'grade' => 'A',
                'marketing_channel' => Harvest::determineMarketingChannel('A'),
                'harvest_date' => now()->format('Y-m-d'),
                'ready_date' => now()->addDays(1)->format('Y-m-d'),
                'photo_path' => 'harvests/brokoli.png',
                'description' => 'Brokoli hijau segar petik pagi dari kebun lereng Tangkuban Perahu. Kuntum rapat padat, bebas ulat, kemasan peti kayu berpendingin.',
                'status' => 'tersedia',
            ],
            [
                'farmer_id' => $sitiId,
                'commodity' => 'Selada Keriting & Romaine',
                'variety' => 'Grand Rapids',
                'quantity' => 400,
                'price_per_kg' => 5000,
                'grade' => 'C',
                'marketing_channel' => Harvest::determineMarketingChannel('C'),
                'harvest_date' => now()->subDays(1)->format('Y-m-d'),
                'ready_date' => now()->format('Y-m-d'),
                'photo_path' => 'harvests/selada.png',
                'description' => 'Selada daun segar dengan sedikit sobekan visual (Grade C). Sangat higienis dan aman pangan, diarahkan untuk pabrik olahan jus sayur atau pakan olahan bernutrisi.',
                'status' => 'tersedia',
            ],
            [
                'farmer_id' => $sitiId,
                'commodity' => 'Stroberi Lembang Segar',
                'variety' => 'Sweet Charlie',
                'quantity' => 180,
                'price_per_kg' => 35000,
                'grade' => 'B',
                'marketing_channel' => Harvest::determineMarketingChannel('B'),
                'harvest_date' => now()->format('Y-m-d'),
                'ready_date' => now()->addDays(1)->format('Y-m-d'),
                'photo_path' => 'harvests/stroberi.png',
                'description' => 'Stroberi petik langsung, ukuran campur manis segar, cocok untuk UMKM pembuat selai, smoothie, atau bakery.',
                'status' => 'tersedia',
            ],
        ];

        foreach ($items as $item) {
            Harvest::create($item);
        }
    }
}

