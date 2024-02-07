<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\laboratory\StoreLaboratoryRequest;
use App\Http\Requests\laboratory\UpdateLaboratoryRequest;
use App\Http\Resources\LaboratoryResource;
use App\Models\Laboratory;
use App\Models\LaboratoryMedia;
use App\Models\User;
use App\Services\CytomineAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Controller for managing laboratories.
 *
 * Handles CRUD operations for laboratories, including
 * listing, creating, updating, and deleting laboratory records.
 */
class LaboratoryController extends Controller
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
     * Display a listing of laboratories.
     *
     * @param Request $request Incoming request instance.
     * @return AnonymousResourceCollection Collection of laboratory resources.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = Auth::user();
        $query = Laboratory::query();
        if ($user && !$user->hasRole(['superAdmin', 'operator'])) {
            $query->where('id', $user->laboratory->id);
        }

        // Search by laboratory details or user's name
        if ($request->has('search')) {
            $searchTerm = $request->get('search');

            $query->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('address', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            })
                ->orWhereHas('user', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                });
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        if ($sortBy === 'name') {
            $query->orderBy('title', $sortOrder);
        }
        $query->orderBy('created_at', 'desc');

        $laboratories = $query->paginate(10);

        return LaboratoryResource::collection($laboratories);
    }

    /**
     * Display the specified laboratory resource.
     *
     * @param string $id The ID of the laboratory.
     * @return LaboratoryResource The requested laboratory resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update media files for a specific laboratory.
     *
     * @param UpdateLaboratoryRequest $request Validated request data with media files.
     * @param string $id The ID of the laboratory for which to update media.
     * @return LaboratoryResource|JsonResponse Updated laboratory resource with new media or error response.
     */
    public function updateMedia(UpdateLaboratoryRequest $request, string $id): LaboratoryResource|JsonResponse
    {
        $laboratory = Laboratory::findOrFail($id);
        try {
            DB::beginTransaction();

            $avatarPath = null;
            $footerPath = null;
            $headerPath = null;
            $signaturePath = null;
            if ($request->hasFile('avatar')) {
                !is_null($laboratory->media->avatar)
                && Storage::disk('public')->delete($laboratory->media->avatar);
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }
            if ($request->hasFile('header')) {
                !is_null($laboratory->media->header)
                && Storage::disk('public')->delete($laboratory->media->header);
                $headerPath = $request->file('header')->store('headers', 'public');
            }
            if ($request->hasFile('signature')) {
                !is_null($laboratory->media->signature)
                && Storage::disk('public')->delete($laboratory->media->signature);
                $signaturePath = $request->file('signature')->store('signatures', 'public');
            }
            if ($request->hasFile('footer')) {
                !is_null($laboratory->media->footer)
                && Storage::disk('public')->delete($laboratory->media->footer);
                $footerPath = $request->file('footer')->store('footers', 'public');
            }

            $laboratory->media->update([
                'avatar' => $avatarPath ?: $laboratory->media->avatar,
                'header' => $headerPath ?: $laboratory->media->header,
                'signature' => $signaturePath ?: $laboratory->media->signature,
                'footer' => $footerPath ?: $laboratory->media->footer,
            ]);

            DB::commit();

            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            DB::rollBack();

            Log::info($e);
            return response()->json(['errors' => 'Error updating laboratory media.'], 500);
        }
    }

    /**
     * Store a newly created laboratory in the database.
     *
     * @param StoreLaboratoryRequest $request Validated request data.
     * @return LaboratoryResource|JsonResponse Newly created laboratory resource or error response.
     */
    public function store(StoreLaboratoryRequest $request): LaboratoryResource|JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->input('fullName'),
                'username' => $request->input('username'),
                'phone' => $request->input('phone'),
                'password' => $request->input('password'),
            ]);

            if ($user) {
                $user->assignRole('laboratory');

                $data = $user->toArray();
                $data['password'] = $request->input('password');
                $cytomineUser = $this->cytomineAuthService->registerUser($data);

                if (!$cytomineUser || !isset($cytomineUser['success'])) {
                    throw new Exception('Cytomine user creation failed');
                }
            }

            $laboratory = Laboratory::create([
                'user_id' => $user->id,
                'title' => $request->input('labName'),
                'address' => $request->input('address'),
                'description' => $request->input('description'),
            ]);

            // Handle file uploads
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }
            if ($request->hasFile('header')) {
                $headerPath = $request->file('header')->store('headers', 'public');
            }
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('signatures', 'public');
            }
            if ($request->hasFile('footer')) {
                $footerPath = $request->file('footer')->store('footers', 'public');
            }

            LaboratoryMedia::create([
                'lab_id' => $laboratory->id,
                'avatar' => $avatarPath ?? null,
                'header' => $headerPath ?? null,
                'signature' => $signaturePath ?? null,
                'footer' => $footerPath ?? null,
            ]);

            DB::commit();

            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            DB::rollBack();

            Log::info('Failed to create laboratory: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['errors' => 'Creating laboratory failed. Try again later.']);
        }
    }

    /**
     * Update the specified laboratory in the database.
     *
     * @param UpdateLaboratoryRequest $request Validated request data.
     * @param string $id The ID of the laboratory to update.
     * @return JsonResponse|LaboratoryResource Updated laboratory resource or error response.
     */
    public function update(UpdateLaboratoryRequest $request, string $id): JsonResponse|LaboratoryResource
    {
        $laboratory = Laboratory::findOrFail($id);
        $user = User::findOrFail($laboratory->user->id);

        try {
            DB::beginTransaction();

            $user->update([
                'name' => !is_null($request->input('fullName')) && $request->input('fullName') !== ""
                    ? $request->input('fullName')
                    : $user->name,
                'username' => !is_null($request->input('username')) && $request->input('username') !== ""
                    ? $request->input('username')
                    : $user->username,
                'phone' => !is_null($request->input('phone')) && $request->input('phone') !== ""
                    ? $request->input('phone')
                    : $user->phone,
                'password' => !is_null($request->input('password')) && $request->input('password') !== ""
                    ? $request->input('password')
                    : $user->password,
            ]);

            $laboratory->update([
                'title' => !is_null($request->input('labName')) && $request->input('labName') !== ""
                    ? $request->input('labName')
                    : $laboratory->title,
                'address' => !is_null($request->input('address')) && $request->input('address') !== ""
                    ? $request->input('address')
                    : $laboratory->address,
                'description' => !is_null($request->input('description')) && $request->input('description') !== ""
                    ? $request->input('description')
                    : $laboratory->description,
            ]);

            DB::commit();

            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            DB::rollBack();

            Log::info($e);
            return response()->json(['errors' => 'Error updating laboratory.'], 500);
        }
    }

    /**
     * Remove the specified laboratory from the database.
     *
     * @param string $id The ID of the laboratory to delete.
     * @return  JsonResponse object indicating success or failure.
     */
    public function destroy(string $id): JsonResponse
    {
        $laboratory = Laboratory::findOrFail($id);
        try {

            if ($laboratory->media) {
                if ($laboratory->media->avatar) {
                    Storage::disk('public')->delete($laboratory->media->avatar);
                }
                if ($laboratory->media->header) {
                    Storage::disk('public')->delete($laboratory->media->header);
                }
                if ($laboratory->media->footer) {
                    Storage::disk('public')->delete($laboratory->media->footer);
                }
                if ($laboratory->media->signature) {
                    Storage::disk('public')->delete($laboratory->media->signature);
                }
            }

//by deleting the user related laboratory will be deleted because of cascade relationship
// then the media row related to the laboratory will be deleted
            $laboratory->user->delete();

            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
