<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\testType\StoreTestTypeRequest;
use App\Http\Resources\TestTypeResource;
use App\Models\TestType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Log;

class TestTypeController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = TestType::query();

        if ($request->has('laboratory')) {
            $labId = $request->input('laboratory'); // Corrected line
            $query->whereHas('prices', function ($query) use ($labId) {
                $query->where('lab_id', $labId);
            });
        }

        $testTypes = $query->get();

        return TestTypeResource::collection($testTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestTypeRequest $request)
    {
        try {
            $testType = TestType::create([
                'title' => $request->input('title'),
                'code' => $request->input('code'),
                'gender' => $request->input('gender'),
                'type' => $request->input('type'),
                'num_layer' => $request->input('numberOfLayers'),
                'micro_step' => $request->input('microStep'),
                'step' => $request->input('step'),
                'z_axis' => $request->input('z'),
                'condenser' => $request->input('condenser'),
                'brightness' => $request->input('brightness'),
                'magnification' => $request->input('magnification'),
                'description' => $request->input('description'),
            ]);

            return new TestTypeResource($testType);
        } catch (Exception $e) {
            Log::info('Failed to create test type: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['error' => 'Creating new test type failed. Try again later.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
