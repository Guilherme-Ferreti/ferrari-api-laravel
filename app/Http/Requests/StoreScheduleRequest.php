<?php

namespace App\Http\Requests;

use App\DTOs\StoreScheduleDTO;
use App\Models\Address;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\TimeOption;
use App\Rules\SameDayOfWeekAsTimeOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
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
            'timeOptionId' => [
                'bail', 'required', 'string', 
                Rule::exists(TimeOption::class, '_id')->whereNull('deleted_at'),
            ],
            'billingAddressId' => [
                'bail', 'required', 'string', 
                Rule::exists(Address::class, '_id')->where('person_id', auth()->user()->person_id),
            ],
            'scheduleAt' => [
                'bail', 'required', 'string', 'date_format:Y-m-d',
                new SameDayOfWeekAsTimeOption
            ],
            'installments' => [
                'bail', 'required', 'integer', 'min:1', 
                'max:'.Schedule::MAX_ALLOWED_INSTALLMENTS
            ],
            'services' => 'bail|required|array|min:1',
            'services.*' => ['bail', 'string', Rule::exists(Service::class, '_id')->whereNull('deleted_at')],
        ];
    }

    public function toDTO(): StoreScheduleDTO
    {
        return new StoreScheduleDTO(
            time_option_id: $this->timeOptionId,
            billing_address_id: $this->billingAddressId,
            schedule_at: $this->scheduleAt,
            installments: $this->installments,
            services: $this->services,
        );
    }
}
