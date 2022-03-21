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

    /**
     * @dataProvider dayOfWeekAndDateProvider
     */
    public function test_rule_works_correctly(int $dayOfWeek, string $date, bool $expected)
    {
        $rules = [
            'timeOptionId' => 'required',
            'scheduleAt' => new SameDayOfWeekAsTimeOption,
        ];

        $input = [
            'timeOptionId' => TimeOption::factory()->create(['day' => $dayOfWeek])->id,
            'scheduleAt' => $date,
        ];

        $this->assertSame($expected, Validator::make($input, $rules)->passes());
    }

    public function dayOfWeekAndDateProvider()
    {
        return [
            'Valid Sunday'    => [0, '2022-03-20', true],
            'Valid Monday'    => [1, '2022-03-21', true],
            'Valid Tuesday'   => [2, '2022-03-22', true],
            'Valid Wednesday' => [3, '2022-03-23', true],
            'Valid Thursday'  => [4, '2022-03-24', true],
            'Valid Friday'    => [5, '2022-03-25', true],
            'Valid Saturday'  => [6, '2022-03-26', true],

            'Invalid Sunday'    => [0, '2022-03-19', false],
            'Invalid Monday'    => [1, '2022-03-20', false],
            'Invalid Tuesday'   => [2, '2022-03-21', false],
            'Invalid Wednesday' => [3, '2022-03-22', false],
            'Invalid Thursday'  => [4, '2022-03-23', false],
            'Invalid Friday'    => [5, '2022-03-24', false],
            'Invalid Saturday'  => [6, '2022-03-25', false],
        ];
    }
}
