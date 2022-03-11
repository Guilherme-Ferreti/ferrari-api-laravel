<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSituation;
use App\Http\Resources\PaymentSituationResource;

class PaymentSituationController extends Controller
{
    public function index()
    {
        $paymentSituations = PaymentSituation::all();

        return PaymentSituationResource::collection($paymentSituations);
    }

    public function show(PaymentSituation $paymentSituation)
    {
        return new PaymentSituationResource($paymentSituation);
    }
}
