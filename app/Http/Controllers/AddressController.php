<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('person_id', request()->user()->person_id)->get();

        return AddressResource::collection($addresses);
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'street'     => 'bail|required|string|max:255',
            'number'     => 'bail|nullable|string|max:15',
            'complement' => 'bail|nullable|string|max:255',
            'district'   => 'bail|required|string|max:255',
            'city'       => 'bail|required|string|max:255',
            'state'      => 'bail|required|string|max:255',
            'country'    => 'bail|required|string|max:255',
            'zipcode'    => 'bail|required|string|max:8',
        ]);

        $attributes['person_id'] = $request->user()->person_id;

        return new AddressResource(Address::create($attributes));
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
