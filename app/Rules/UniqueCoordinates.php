<?php

namespace App\Rules;

use App\Models\Slide;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueCoordinates implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $requiredKeys = ['sw_x', 'sw_y', 'ne_x', 'ne_y'];
        foreach ($requiredKeys as $key) {
            if (!isset(request()->coordinates[$key])) {
                $fail("The $key  is required in the coordinates.");
                return;
            }
        }

        $sw_x = (float)request()->coordinates['sw_x'];
        $sw_y = (float)request()->coordinates['sw_y'];
        $ne_x = (float)request()->coordinates['ne_x'];
        $ne_y = (float)request()->coordinates['ne_y'];

        if ($sw_x === $sw_y && $sw_x === $ne_x && $sw_x === $ne_y) {
            $fail('All coordinates can not have the same value.');
            return;
        }

        if ($sw_x === $ne_x || $sw_y === $ne_y) {
            $fail('Coordinates must form a rectangle not a line!');
            return;
        }

        if ($sw_x > $ne_x || $sw_y > $ne_y) {
            $fail('Please review the coordinates. SW : South-West, NE: North-East.');
            return;
        }
        
        $existingPoint = Slide::where([
            ['sw_x', '>=', $sw_x - 0.0001],
            ['sw_x', '<=', $sw_x + 0.0001],
            ['sw_y', '>=', $sw_y - 0.0001],
            ['sw_y', '<=', $sw_y + 0.0001],
        ])
            ->orWhere([
                ['ne_x', '>=', $ne_x - 0.0001],
                ['ne_x', '<=', $ne_x + 0.0001],
                ['ne_y', '>=', $ne_y - 0.0001],
                ['ne_y', '<=', $ne_y + 0.0001],
            ])
            ->get();

        if ($existingPoint->count() > 0) {
            $fail('The slides cannot overlap.');
        }

        $existingPosition = Slide::where([
            ['sw_x', '>=', $sw_x - 0.0001],
            ['sw_x', '<=', $sw_x + 0.0001],
            ['sw_y', '>=', $sw_y - 0.0001],
            ['sw_y', '<=', $sw_y + 0.0001],
            ['ne_x', '>=', $ne_x - 0.0001],
            ['ne_x', '<=', $ne_x + 0.0001],
            ['ne_y', '>=', $ne_y - 0.0001],
            ['ne_y', '<=', $ne_y + 0.0001],
        ])->get();

        if ($existingPosition->count() > 0) {
            $fail('This position already exists.');
        }

    }


}
