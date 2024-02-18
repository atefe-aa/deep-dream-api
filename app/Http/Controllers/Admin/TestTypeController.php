<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\testType\StoreTestTypeRequest;
use App\Http\Resources\TestTypeResource;
use App\Models\Test;
use App\Models\TestType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

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

            $labId = $request->input('laboratory');
            $noPrice = $request->has('noPrice');
            if ($noPrice) {
                //for create new test type price form to prevent duplicate pair of lab_id - test_typ_id
                //return only test types with no price record for the lab
                $query->whereDoesntHave('prices', function ($query) use ($labId) {
                    $query->where('lab_id', $labId);
                });
            } else {
                //for registration form we need test types with price for the lab
                $query->whereHas('prices', function ($query) use ($labId) {
                    $query->where('lab_id', $labId);
                });
            }

        }

        $sortBy = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $noPaginate = $request->has('noPaginate');

        if ($noPaginate) {
//            in the forms we need all the test types but in the tables we paginate the data
            $testTypes = $query->orderBy('title')->get();
        } else {
            $testTypes = $query->orderBy($sortBy, $order)->paginate(10);
        }

        return TestTypeResource::collection($testTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTestTypeRequest $request
     * @return TestTypeResource|JsonResponse
     */
    public function store(StoreTestTypeRequest $request): JsonResponse|TestTypeResource
    {
        try {
            $testType = new TestType([
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

//            $testType->setDefaultValues($request->input('magnification'));
            $testType->save();

            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Failed to create test type: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['errors' => 'Creating new test type failed. Try again later.']);
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
        $testType = TestType::findOrFail($id);
        $this->authorize('delete', $testType);
        try {
            $testsWithTestType = Test::where('test_type_id', $testType->id)->get();
            Log::info($testsWithTestType);
            if ($testsWithTestType->count() > 0) {
                return response()
                    ->json([
                        'message' => 'Test type can not be deleted, because it has been used in some tests.'
                    ], 409);
            }
            $testType->delete();
            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Failed to delete counsellor : ' . $e->getMessage(), ['counsellor id' => $id]);
            return response()->json(['errors' => 'Deleting counsellor failed. Please try again later.'], 500);
        }
    }
}
