<?php

namespace App\Http\Controllers;

use App\Http\Requests\registration\StoreRegistrationRequest;
use App\Http\Resources\RegistrationResource;
use App\Models\Patient;
use App\Models\Test;
use App\Services\CytomineProjectService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;


class RegistrationController extends Controller
{
    protected CytomineProjectService $cytomineProjectService;

    public function __construct(CytomineProjectService $cytomineProjectService)
    {
        $this->cytomineProjectService = $cytomineProjectService;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Test::with(['testType', 'laboratory', 'patient']);

        $user = Auth::user();
        if ($user && !$user->hasRole(['superAdmin', 'operator'])) {
            $laboratoryId = $user->laboratory->id;
            $query->where('lab_id', $laboratoryId);
        }

        if ($request->has('search')) {
            $searchTerm = $request->get('search');

            $query->where(function ($query) use ($searchTerm) {
                $query->where('sender_register_code', 'like', '%' . $searchTerm . '%')
                    ->orWhere('doctor_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%');
            })
                ->orWhereHas('testType', function ($query) use ($searchTerm) {
                    $query->where('title', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('patient', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('national_id', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('laboratory', function ($query) use ($searchTerm) {
                    $query->where('title', 'like', '%' . $searchTerm . '%');
                });
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        switch ($sortBy) {
            case 'patient':
                $query->join('patients', 'tests.patient_id', '=', 'patients.id')
                    ->orderBy('patients.name', $sortOrder)
                    ->select('tests.*');
                break;
            case 'sender-laboratory':
                $query->join('laboratories', 'tests.lab_id', '=', 'laboratories.id')
                    ->orderBy('laboratories.title', $sortOrder)
                    ->select('tests.*');
                break;
            case 'admit-number':
                $query->orderBy('id', $sortOrder);
                break;
            case 'date':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'progress':
                $query->orderBy('status', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $tests = $query->paginate(10);

        return RegistrationResource::collection($tests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegistrationRequest $request): RegistrationResource|JsonResponse
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            //to prevent duplicate rows for a patient
            $patient = Patient::where('national_id', $request->input('nationalId'))->first();
            if ($patient) {
                $patient->update([
                    'age' => $request->input('age'),
                    'ageUnit' => $request->input('ageUnit'),
                ]);
            } else {
                $patient = Patient::create([
                    'name' => $request->input('name'),
                    'national_id' => $request->input('nationalId'),
                    'age' => $request->input('age'),
                    'ageUnit' => $request->input('ageUnit'),
                    'gender' => $request->input('gender'),
                ]);
            }


            $test = new Test([
                'patient_id' => $patient->id,
                'lab_id' => $user && $user->laboratory
                    ? $user->laboratory->id
                    : $request->input('laboratoryId'),
                'sender_register_code' => $request->input('senderRegisterCode'),
                'test_type_id' => $request->input('testType'),
                'doctor_name' => $request->input('doctorName'),
                'num_slide' => $request->input('numberOfSlides'),
                'description' => $request->input('description'),
            ]);


            $test->setPriceAttribute();

            $test->save();

            $projectName = $request->input('name') . "-" . $test->id;
            $username = $user->hasRole(['superAdmin', 'operator'])
                ? env('CYTOMINE_ADMIN_USERNAME')
                : $user->username;

            $projectResponse = $this->cytomineProjectService->createProject($projectName, $username);

            if (isset($projectResponse['errors']) && !is_null($projectResponse['errors'])) {
                throw new RuntimeException($projectResponse['errors']);
            }

            $test->update(['project_id' => $projectResponse['data']['projectId']]);

            DB::commit();

            return response()->json(['data' => 'success']);

        } catch (Exception $e) {
            DB::rollBack();

            Log::info($e);
            return response()->json(['errors' => 'Error creating registration.'], 500);

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
    public function show(string $id): RegistrationResource
    {
        $test = Test::findOrFail($id);

        return new RegistrationResource($test);
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        $test = Test::findOrFail($id);
        $this->authorize('delete', $test);
        try {
            $test->delete();
            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Failed to delete test : ' . $e->getMessage(), ['test id' => $id]);
            return response()->json(['errors' => 'Deleting test failed. Please try again later.'], 500);
        }
    }
}
