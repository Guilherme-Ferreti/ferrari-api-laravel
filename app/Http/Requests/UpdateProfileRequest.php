<?php

namespace App\Http\Requests;

use App\DTOs\UpdateProfileDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'email'     => ['bail', 'required', 'string', 'max:255', 'email', Rule::unique('users', 'email')->ignore(request()->user())],
            'name'      => 'bail|required|string|max:255',
            'birthAt'   => 'bail|required|nullable|date_format:Y-m-d',
            'phone'     => 'bail|required|string|max:16|regex:/^\d+$/',
            'document'  => 'bail|required|string|max:16|regex:/^\d+$/',
        ];
    }

    public function toDTO()
    {
        return new UpdateProfileDTO(
            email: $this->email,
            name: $this->name,
            birth_at: $this->birthAt,
            phone: $this->phone,
            document: $this->document,
        );
    }
}
