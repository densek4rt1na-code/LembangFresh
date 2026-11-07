<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Demand;
use App\Models\User;

class DemandSeeder extends Seeder
{
    public function run(): void
    {
        $buyerSalad = User::where('email', 'salad@lembangfresh.test')->first();
        $buyerId = $buyerSalad ? $buyerSalad->id : 4;

        $demands = [
            [
                'buyer_id' => $buyerId,
                'commodity' => 'Wortel Brastagi Lembang',
                'quantity' => 300,
                'grade' => 'B',
                'max_price_per_kg' => 5000,
                'notes' => 'Kebutuhan mingguan salad box fresh untuk katering sehat Lembang.',
                'status' => 'terbuka',
            ],
            [
                'buyer_id' => $buyerId,
                'commodity' => 'Tomat Beef & Cherry Lembang',
                'quantity' => 200,
                'grade' => 'A',
                'max_price_per_kg' => 9000,
                'notes' => 'Untuk sajian fresh salad buah dan sayur di restoran.',
                'status' => 'terbuka',
            ],
            [
                'buyer_id' => $buyerId,
                'commodity' => 'Kentang Granola Lembang',
                'quantity' => 400,
                'grade' => 'A',
                'max_price_per_kg' => 13000,
                'notes' => 'Untuk menu mashed potato dan french fries organik.',
                'status' => 'terbuka',
            ],
        ];

        foreach ($demands as $demand) {
            Demand::create($demand);
        }
    }
}

