<?php

namespace App\Http\Controllers;

use App\Helpers\JsonHelper;
use App\Http\Requests\report\StoreReportRequest;
use App\Http\Resources\ReportTemplateResource;
use App\Models\Report;
use App\Models\ReportTemplate;
use App\Models\Test;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $reports = ReportTemplate::all();
        return ReportTemplateResource::collection($reports);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        $user = Auth::user();
        $testId = $request->testId;
        $test = Test::findOrFail($testId);
        $this->authorize('update', $test);

        try {
            $existingReport = Report::where('test_id', $testId)->first();
            if ($existingReport) {
                if ($existingReport->delete()) {
                    Report::create([
                        'test_id' => $testId,
                        'user_id' => $user->id,
                        'template_id' => $request->id,
                        'data' => JsonHelper::encodeJson($request->sections)
                    ]);
                }
            } else {
                Report::create([
                    'test_id' => $testId,
                    'user_id' => $user->id,
                    'template_id' => $request->id,
                    'data' => JsonHelper::encodeJson($request->sections)
                ]);
                $test->update([
                    'status' => 'answered'
                ]);
            }


            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Something went wrong creating a report: ' . $e->getMessage());
            return response()->json(['errors' => 'Something went wrong creating a report.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $report = Report::where('test_id', $id)->first();
        if ($report) {
            $test = Test::findOrFail($report->test_id);
            $this->authorize('update', $test);

            return new ReportTemplateResource($report);
        }
        return response()->json(['error' => 'failed'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
