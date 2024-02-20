<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class StatisticsController extends Controller
{
//    public function chart(Request $request): array
//    {
//        $fromDate = $request->get('fromDate');
//        $toDate = $request->get('toDate');
//        $laboratories = $request->get('laboratories');
//        $testTypes = $request->get('testTypes');
//        $x = $request->get('x');
//        $y = $request->get('y');
//
//        $testsQuery = Test::query();
//
//        if ($fromDate && $toDate) {
//            $testsQuery->whereBetween('created_at', [
//                DateHelper::toGregorian($fromDate),
//                DateHelper::toGregorian($toDate)
//            ]);
//        }
//
//        if (!empty($laboratories)) {
//            $testsQuery->whereIn('lab_id', $laboratories);
//        }
//
//        if (!empty($testTypes)) {
//            $testsQuery->whereIn('test_type_id', $testTypes);
//        }
//
//        $tests = $testsQuery->get();
//
//        $groupedData = [];
//        if ($x === 'laboratories') {
//            $groupedData = $tests->groupBy('lab_id');
//        } elseif ($x === 'testTypes') {
//            $groupedData = $tests->groupBy('test_type_id');
//        }
//
//        $totals = [];
//        $groupedResults = [];
//
//        foreach ($groupedData as $key => $group) {
//            $dataValue = $y === 'price' ? $group->sum('price') : $group->count();
//            $title = $x === 'laboratories' ? $group->first()->laboratory->title : $group->first()->testType->title;
//            $groupedResults[] = ['title' => $title, 'value' => $dataValue];
//        }
//
//        // Sort the grouped results from highest to lowest
//        usort($groupedResults, static function ($a, $b) {
//            return $b['value'] - $a['value'];
//        });
//
//        $seriesData = [];
//        $categories = [];
//        foreach ($groupedResults as $result) {
//            $seriesData[] = $result['value'];
//            $categories[] = $result['title'];
//        }
//
//        $series [] = [
//            "name" => $y === 'price' ? "Total Price" : "Total Tests",
//            'data' => $seriesData
//        ];
//
//        if ($y === 'price') {
//            $totals[] = ["title" => "Total Price", "unit" => "(R)", "value" => $tests->sum('price')];
//        } else {
//            $totals[] = ["title" => "Total Tests", "unit" => "", "value" => $tests->count()];
//        }
//
//        $dateRange = Jalalian::fromDateTime($tests->min('created_at'))->format('Y/m/d') . ' - ' . Jalalian::fromDateTime($tests->max('created_at'))->format('Y/m/d');
//        $totals[] = ["title" => "Date Range", "unit" => "", "value" => $dateRange];
//
//        return [
//            'data' => [
//                "totals" => $totals,
//                "series" => $series,
//                "xAxisCategories" => $categories,
//            ]
//        ];
//    }


    public function chart(Request $request): array
    {
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');
        $laboratories = $request->get('laboratories');
        $testTypes = $request->get('testTypes');
        $x = $request->get('x');
        $y = $request->get('y');

        $testsQuery = Test::with(['laboratory', 'testType']);

        if ($fromDate && $toDate) {
            $testsQuery->whereBetween('created_at', [
                DateHelper::toGregorian($fromDate),
                DateHelper::toGregorian($toDate)
            ]);
        }

        if (!empty($laboratories)) {
            $testsQuery->whereIn('lab_id', $laboratories);
        }

        if (!empty($testTypes)) {
            $testsQuery->whereIn('test_type_id', $testTypes);
        }

        $tests = $testsQuery->get();

        $series = [];
        $categories = [];
        $totals = [];

        // Check if both filters are applied
        if (!empty($laboratories) && !empty($testTypes) && $x === 'testTypes') {
            $labGrouped = $tests->groupBy('lab_id');
            foreach ($labGrouped as $labId => $labTests) {
                $labSeriesData = [];
                $testTypeGrouped = $labTests->groupBy('test_type_id');
                foreach ($testTypeGrouped as $testTypeId => $testsGroup) {
                    $value = $y === 'price' ? $testsGroup->sum('price') : $testsGroup->count();
                    $labSeriesData[] = $value;
                }
                $series[] = [
                    'name' => $labTests->first()->laboratory->title,
                    'data' => $labSeriesData
                ];
                $categories = $testTypeGrouped->map(function ($item, $key) {
                    return $item->first()->testType->title;
                })->values()->all();
            }
        } else {

            $groupedData = [];
            if ($x === 'laboratories') {
                $groupedData = $tests->groupBy('lab_id');
            } elseif ($x === 'testTypes') {
                $groupedData = $tests->groupBy('test_type_id');
            }

            $groupedResults = [];

            foreach ($groupedData as $key => $group) {
                $dataValue = $y === 'price' ? $group->sum('price') : $group->count();
                $title = $x === 'laboratories' ? $group->first()->laboratory->title : $group->first()->testType->title;
                $groupedResults[] = ['title' => $title, 'value' => $dataValue];
            }

            // Sort the grouped results from highest to lowest
            usort($groupedResults, static function ($a, $b) {
                return $b['value'] - $a['value'];
            });

            $seriesData = [];
            foreach ($groupedResults as $result) {
                $seriesData[] = $result['value'];
                $categories[] = $result['title'];
            }

            $series [] = [
                "name" => $y === 'price' ? "Total Price" : "Total Tests",
                'data' => $seriesData
            ];
        }

        if ($y === 'price') {
            $totals[] = ["title" => "Total Price", "unit" => "(R)", "value" => $tests->sum('price')];
        } else {
            $totals[] = ["title" => "Total Tests", "unit" => "", "value" => $tests->count()];
        }

        $dateRange = Jalalian::fromDateTime($tests->min('created_at'))->format('Y/m/d') . ' - ' . Jalalian::fromDateTime($tests->max('created_at'))->format('Y/m/d');
        $totals[] = ["title" => "Date Range", "unit" => "", "value" => $dateRange];

        return [
            'data' => [
                "totals" => $totals,
                "series" => $series,
                "xAxisCategories" => $categories,
            ]
        ];
    }


}
