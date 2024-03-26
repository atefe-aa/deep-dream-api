<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AgeHelper;
use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use App\Models\Test;
use App\Models\TestType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class StatisticsController extends Controller
{

    public function chart(Request $request): JsonResponse
    {
        $user = Auth::user();

        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');

        $laboratories = $request->get('laboratories');
        $testTypes = $request->get('testTypes');

        $y = $request->get('y');
        $x = $request->get('x');

        if ($user && $user->hasRole(['operator'])) {
            return response()->json(['errors' => 'Not Authenticated'], 403);
        }

        $labId = $user && !$user->hasRole(['superAdmin']) ? $user->laboratory->id : null;

        if ($x === 'laboratories' && $labId) {
            return response()->json(['errors' => 'Not Authenticated'], 403);
        }
        $testTypeQuery = !empty($testTypes) ? TestType::whereIn('id', $testTypes) : TestType::query();
        if ($x === 'laboratories') {
            $allData = !empty($laboratories) ? Laboratory::whereIn('id', $laboratories)->get() : Laboratory::all()->keyBy('id');
            $testsQuery = Test::with(['laboratory']);
            $groupBy = 'lab_id';
        } else {
            $allData = $labId
                ? $testTypeQuery->whereHas('prices', static function ($query) use ($labId) {
                    $query->where('lab_id', $labId);
                })->get()
                : $testTypeQuery->get();
            $testsQuery = $labId
                ? Test::where('lab_id', $labId)->with(['testType'])
                : Test::with(['testType']);
            $groupBy = 'test_type_id';
        }

        if ($fromDate && $toDate) {
            $testsQuery->whereBetween('created_at', [
                AgeHelper::toGregorian($fromDate),
                AgeHelper::toGregorian($toDate)
            ]);
        }
        if (!empty($laboratories)) {
            $testsQuery->whereIn('lab_id', $laboratories);
        }

        $tests = $testsQuery->get();

        $groupedData = $tests->groupBy($groupBy);
        $groupedResults = [];
        foreach ($allData as $data) {
            $dataValue = 0;
            $title = $data->title;
            foreach ($groupedData as $group) {
                $groupDataId = $x === 'laboratories' ? $group->first()->lab_id : $group->first()->test_type_id;
                if ($groupDataId === $data->id) {
                    $dataValue = $y === 'price' ? $group->sum('price') : $group->count();
                }
            }
            $groupedResults[] = ['title' => $title, 'value' => $dataValue];
        }

        // Sort the grouped results from highest to lowest
        usort($groupedResults, static function ($a, $b) {
            return $b['value'] - $a['value'];
        });

        $seriesData = [];
        $categories = [];
        foreach ($groupedResults as $result) {
            $seriesData[] = $result['value'];
            $categories[] = $result['title'];
        }
        $series [] = [
            "name" => $y === 'price' ? "Total Price" : "Total Tests",
            'data' => $seriesData
        ];

        if ($y === 'price') {
            $totals[] = ["title" => "Total Price", "unit" => "(R)", "value" => $tests->sum('price')];
        } else {
            $totals[] = ["title" => "Total Tests", "unit" => "", "value" => $tests->count()];
        }

        $dateRange = Jalalian::fromDateTime($tests->min('created_at'))->format('Y/m/d') . ' - ' . Jalalian::fromDateTime($tests->max('created_at'))->format('Y/m/d');
        $totals[] = ["title" => "Date Range", "unit" => "", "value" => $dateRange];

        return response()->json([
            'data' => [
                "totals" => $totals,
                "series" => $series,
                "xAxisCategories" => array_values($categories),
            ]
        ]);
    }

    public function radarChart(Request $request): JsonResponse
    {
        $user = Auth::user();
        if ($user && !$user->hasRole(['superAdmin'])) {
            return response()->json(['errors' => 'Not Authenticated'], 403);
        }

        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');

        $laboratories = $request->get('laboratories');
        $testTypes = $request->get('testTypes');

        $y = $request->get('y');

        $allTestTypes = $testTypes && !empty($testTypes)
            ? TestType::whereIn('id', $testTypes)->get()
            : TestType::take(5)->get();
        $allLabs = $laboratories && !empty($laboratories)
            ? Laboratory::whereIn('id', $laboratories)->get()
            : Laboratory::take(5)->get();


        $testsQuery = Test::with(['testType', 'laboratory']);

        if ($fromDate && $toDate) {
            $testsQuery->whereBetween('created_at', [
                AgeHelper::toGregorian($fromDate),
                AgeHelper::toGregorian($toDate)
            ]);
        }

        $tests = $testsQuery->get();
        $totalValues = 0;
        $series = [];
        $categories = [];

        foreach ($allLabs as $lab) {
            $name = $lab->title;
            $labSeriesData = [];
            foreach ($allTestTypes as $testType) {
                $categories[$testType->id] = $testType->title;
                $value = 0;
                $labGrouped = $tests->groupBy('lab_id');
                foreach ($labGrouped as $labId => $labTests) {

                    if ($labId === $lab->id) {
                        $testTypeGrouped = $labTests->groupBy('test_type_id');
                        foreach ($testTypeGrouped as $testTypeId => $testsGroup) {
                            if ($testTypeId === $testType->id) {
                                $value = $y === 'price' ? $testsGroup->sum('price') : $testsGroup->count();
                            }

                        }
                    }
                }
                $labSeriesData[] = $value;
                $totalValues += $value;
            }
            $series[] = [
                'name' => $name,
                'data' => $labSeriesData
            ];
        }


        if ($y === 'price') {
            $totals[] = ["title" => "Total Price", "unit" => "(R)", "value" => $totalValues];
        } else {
            $totals[] = ["title" => "Total Tests", "unit" => "", "value" => $totalValues];
        }

        $dateRange = Jalalian::fromDateTime($tests->min('created_at'))->format('Y/m/d') . ' - ' . Jalalian::fromDateTime($tests->max('created_at'))->format('Y/m/d');
        $totals[] = ["title" => "Date Range", "unit" => "", "value" => $dateRange];

        return response()->json([
            'data' => [
                "totals" => $totals,
                "series" => $series,
                "xAxisCategories" => array_values($categories),
            ]
        ]);
    }

}
