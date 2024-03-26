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

        $slideId = request()?->route('slide');

//        $overlapArea = Slide::where([
//            ['id', '!=', $slideId],
//            // and $sw_x is not in the range between 'ne_x' and 'sw_x'
//            // and $sw_y is not in the range between 'ne_y' and 'sw_y'
//        ])
//            ->get();

        $overlapArea = Slide::where('id', '!=', $slideId)
            ->where(function ($query) use ($sw_x, $sw_y, $ne_x, $ne_y) {
                // Ensure that the southwest corner of the current slide
                // is not within the range of any existing slides
                $query->where(function ($q) use ($sw_x, $sw_y, $ne_x, $ne_y) {
                    $q->where([
                        ['sw_x', '<', $sw_x],
                        ['ne_x', '>', $sw_x],
                        ['sw_y', '<', $sw_y],
                        ['ne_y', '>', $sw_y]
                    ]);
                })
                    ->orWhere(function ($q) use ($sw_x, $sw_y, $ne_x, $ne_y) {
                        $q->where([
                            ['sw_x', '<', $ne_x],
                            ['ne_x', '>', $ne_x],
                            ['sw_y', '<', $ne_y],
                            ['ne_y', '>', $ne_y]
                        ]);
                    })
                    ->orWhere(function ($q) use ($sw_x, $sw_y, $ne_x, $ne_y) {
                        $q->where([
                            ['sw_x', '>', $sw_x],
                            ['sw_x', '<', $ne_x],
                            ['sw_y', '>', $sw_y],
                            ['sw_y', '<', $ne_y]
                        ]);
                    })
                    ->orWhere(function ($q) use ($sw_x, $sw_y, $ne_x, $ne_y) {
                        $q->where([
                            ['ne_x', '>', $sw_x],
                            ['ne_x', '<', $ne_x],
                            ['ne_y', '>', $sw_y],
                            ['ne_y', '<', $ne_y]
                        ]);
                    });
            })
            ->get();


        if ($overlapArea->count() > 0) {
            $fail('The slides cannot overlap.');
        }

        $existingPosition = Slide::where([
            ['id', '!=', $slideId],
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
