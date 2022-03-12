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
            'Payment Pending', 
            'Canceled', 
            'Payment Approved', 
            'Payment Reversed', 
            'Handling', 
            'Sent',
        ];

        array_map(
            fn ($item) => PaymentSituation::create(['name' => $item]), 
            $paymentSituations
        );
    }
}
