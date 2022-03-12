<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', Service::class);

        $attributes = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
        ]);

        $service = Service::create($attributes);

        return $this->respondCreated(new ServiceResource($service));
    }
}
