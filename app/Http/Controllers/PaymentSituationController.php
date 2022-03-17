<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function store(Request $request)
    {
        $this->authorize('create', PaymentSituation::class);

        $attributes = $request->validate([
            'name' => 'required|string|max:255|unique:payment_situations',
        ]);

        $attributes['_id'] = PaymentSituation::count() + 1;

        $paymentSituation = PaymentSituation::create($attributes);

        return $this->respondCreated(new PaymentSituationResource($paymentSituation));
    }

    public function update(Request $request, PaymentSituation $paymentSituation)
    {
        $this->authorize('update', PaymentSituation::class);

        $attributes = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique(PaymentSituation::class, 'name')->ignoreModel($paymentSituation),
            ],
        ]);

        $paymentSituation->update($attributes);

        return new PaymentSituationResource($paymentSituation);
    }

    public function destroy(PaymentSituation $paymentSituation)
    {
        $this->authorize('delete', PaymentSituation::class);

        $paymentSituation->delete();

        return $this->respondNoContent();
    }

    public function restore(PaymentSituation $paymentSituation)
    {
        $this->authorize('restore', PaymentSituation::class);

        $paymentSituation->restore();

        return new PaymentSituationResource($paymentSituation);
    }
}
