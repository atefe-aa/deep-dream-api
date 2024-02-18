<?php

namespace App\Http\Controllers;

use App\Http\Requests\counsellor\StoreCounsellorRequest;
use App\Http\Resources\CounsellorResource;
use App\Models\Counsellor;
use App\Services\CytomineAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class CounsellorController extends Controller
{

    protected CytomineAuthService $cytomineAuthService;

    /**
     * Constructor for LaboratoryController.
     *
     * @param CytomineAuthService $cytomineAuthService Service for Cytomine authentication.
     */
    public function __construct(CytomineAuthService $cytomineAuthService)
    {
        $this->cytomineAuthService = $cytomineAuthService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Counsellor::query();
        $user = Auth::user();

        if ($user && !$user->hasRole(['superAdmin', 'operator'])) {
            $labId = $user->laboratory->id;
            $query->where('lab_id', $labId);
        }

        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($query) use ($searchTerm) {
                return $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                    ->orwhere('description', 'like', '%' . $searchTerm . '%');
            })
                ->orWhereHas('laboratory', function ($query) use ($searchTerm) {
                    return $query->where('title', 'like', '%' . $searchTerm . '%');
                });

        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);


        if ($request->get('noPaginate', false)) {
            $counsellors = $query->get();
        } else {
            $counsellors = $query->paginate(10);
        }

        return CounsellorResource::collection($counsellors);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCounsellorRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['errors' => 'Unauthorized'], 401);
        }

        $labId = $user->hasRole("superAdmin") ? $request->input('labId') : $user->laboratory->id;

        try {
            DB::beginTransaction();

            $counsellor = Counsellor::create([
                'lab_id' => $labId,
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'description' => $request->input('description'),
            ]);


            if (!$counsellor) {
                throw new Exception('Failed to create counsellor');
            }


            $username = $request->input('phone') . $counsellor->id;
            $password = $request->input('phone');
            $data = [
                "name" => $request->input('name'),
                'password' => $password,
                'username' => $username
            ];

            $cytomineUser = $this->cytomineAuthService->registerUser($data);

            // Check for successful external service registration
            if (!$cytomineUser || !isset($cytomineUser['user'])) {

                throw new RuntimeException('Cytomine user creation failed');
            }

            $counsellor->update(['cytomine_user_id' => $cytomineUser['user']['id']]);

            DB::commit();
            return response()->json(['data' => 'Counsellor created successfully'], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create counsellor: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['errors' => 'Creating counsellor failed. Try again later.'], 500);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $counsellor = Counsellor::findOrFail($id);
        try {
            $counsellor->delete();
            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Failed to delete counsellor : ' . $e->getMessage(), ['counsellor id' => $id]);
            return response()->json(['errors' => 'Deleting counsellor failed. Please try again later.'], 500);
        }
    }
}
