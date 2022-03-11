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
            'Aguardando Pagamento', 
            'Cancelado', 
            'Pagamento Aprovado', 
            'Pagamento Estornado', 
            'Em mediação', 
            'Enviado',
        ];

        array_map(
            fn ($item) => PaymentSituation::create(['name' => $item]), 
            $paymentSituations
        );
    }
}
