<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();

        return ServiceResource::collection($services);
    }

    public function show(Service $service)
    {
        return new ServiceResource($service);
    }

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

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $attributes = $request->validate([
            'name'        => 'string|max:255',
            'description' => 'string|max:255',
            'price'       => 'numeric|min:0',
        ]);

        $service->update($attributes);

        return new ServiceResource($service);
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        $service->delete();

        return $this->respondNoContent();
    }
}
