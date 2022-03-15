<?php

namespace App\Rules;

use App\Models\TimeOption;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

class SameDayOfWeekAsTimeOption implements Rule, DataAwareRule
{
    protected array $data = [];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $timeOption = TimeOption::find($this->data['timeOptionId']);

        if (! $timeOption) {
            return false;
        }

        $carbon = Carbon::createFromFormat('Y-m-d', $value);

        return $carbon->dayOfWeek == $timeOption->day;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute day of week must be the same as given time option day of week.';
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
 
        return $this;
    }
}
