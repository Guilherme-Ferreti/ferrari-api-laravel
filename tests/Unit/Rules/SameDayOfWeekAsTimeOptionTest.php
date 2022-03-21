<?php

namespace Tests\Unit\Rules;

use App\Models\TimeOption;
use App\Rules\SameDayOfWeekAsTimeOption;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SameDayOfWeekAsTimeOptionTest extends TestCase
{
    use DatabaseMigrations;

    public function test_rule_passes_when_given_valid_data()
    {
        $rules = [
            'scheduleAt' => new SameDayOfWeekAsTimeOption,
            'timeOptionId' => 'required',
        ];

        $input = [
            'scheduleAt' => '2022-03-21', // Monday
            'timeOptionId' => TimeOption::factory()->create(['day' => 1])->id,
        ];

        $this->assertTrue(Validator::make($input, $rules)->passes());
    }

    public function test_rule_fails_when_given_invalid_data()
    {
        $rules = [
            'scheduleAt' => new SameDayOfWeekAsTimeOption,
            'timeOptionId' => 'required',
        ];

        $input = [
            'scheduleAt' => '2022-03-22', // Tuesday
            'timeOptionId' => TimeOption::factory()->create(['day' => 1])->id,
        ];

        $this->assertTrue(Validator::make($input, $rules)->fails());
    }
}
