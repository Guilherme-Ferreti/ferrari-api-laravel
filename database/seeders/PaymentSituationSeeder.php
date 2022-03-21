<?php

namespace Database\Seeders;

use App\Models\PaymentSituation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSituationSeeder extends Seeder
{
    public function run()
    {
        $paymentSituations = [
            1 => 'Payment Pending', 
            2 => 'Canceled', 
            3 => 'Payment Approved', 
            4 => 'Payment Reversed', 
            5 => 'Handling', 
            6 => 'Sent',
        ];

        foreach ($paymentSituations as $key => $name) {
            PaymentSituation::create([
                '_id' => (string) $key,
                'name' => $name,
            ]);
        }
    }
}
