<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attributes = $request->validate([
            'email'     => 'bail|required|string|max:255|email|unique:users',
            'name'      => 'bail|required|string|max:255',
            'birthAt'   => 'bail|nullable|date_format:Y-m-d',
            'password'  => ['bail', 'required', 'string', Password::defaults()],
            'phone'     => 'bail|required|string|max:16|regex:/^\d+$/',
            'document'  => 'bail|required|string|max:16|regex:/^\d+$/',
        ]);

        $person = Person::create($attributes);

        $person->user()->create($attributes);

        $person->load('user');

        return $person;
    }
}
