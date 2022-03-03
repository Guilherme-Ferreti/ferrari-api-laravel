<?php

namespace App\Http\Requests;

use App\DTO\RegisterDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'     => 'bail|required|string|max:255|email|unique:users',
            'name'      => 'bail|required|string|max:255',
            'birthAt'  => 'bail|nullable|date_format:Y-m-d',
            'password'  => ['bail', 'required', 'string', Password::defaults()],
            'phone'     => 'bail|required|string|max:16|regex:/^\d+$/',
            'document'  => 'bail|required|string|max:16|regex:/^\d+$/',
        ];
    }

    public function toDTO(): RegisterDTO
    {
        return new RegisterDTO(
            email: $this->email,
            name: $this->name,
            birth_at: $this->birthAt,
            password: $this->password,
            phone: $this->phone,
            document: $this->document,
        );
    }
}
