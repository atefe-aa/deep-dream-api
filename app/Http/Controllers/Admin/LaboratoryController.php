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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaboratoryController extends Controller
{
    protected CytomineAuthService $cytomineAuthService;

    public function __construct(CytomineAuthService $cytomineAuthService)
    {
        $this->cytomineAuthService = $cytomineAuthService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = Auth::user();
        $query = Laboratory::query();
        if($user && !$user->hasRole(['superAdmin','operator'])){
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
        if($sortBy  === 'name'){
            $query->orderBy('title', $sortOrder);
        }
        $query->orderBy('created_at', 'desc');

        $laboratories = $query->paginate(10);

        return LaboratoryResource::collection($laboratories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLaboratoryRequest $request): LaboratoryResource | JsonResponse
    {
        try{
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->input('fullName'),
                'username' => $request->input('username'),
                'phone' => $request->input('phone'),
                'password' => $request->input('password'),
            ]);

            if($user) {
                $user->assignRole('laboratory');

                $data = $user->toArray();
                $data['password'] = $request->input('password');
                $cytomineUser = $this->cytomineAuthService->registerUser($data);
                \Log::info($cytomineUser);

                if (!$cytomineUser || !isset($cytomineUser['success'])) {
                    throw new \Exception('Cytomine user creation failed');
                }
            }

            $laboratory = Laboratory::create([
                'user_id' => $user->id,
                'title' => $request->input('labName'),
                'address' => $request->input('address'),
                'description' => $request->input('description'),
            ]);

            // Handle file uploads
            if($request->hasFile('avatar')){
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }
            if($request->hasFile('header')){
                $headerPath = $request->file('header')->store('headers', 'public');
            }
            if($request->hasFile('signature')){
                $signaturePath = $request->file('signature')->store('signatures', 'public');
            }
            if($request->hasFile('footer')){
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

            return new LaboratoryResource($laboratory);
        }catch (\Exception $e){
            DB::rollBack();

            \Log::info($e);
            return response()->json(['error' => 'Error creating laboratory.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLaboratoryRequest $request, string $id): JsonResponse|LaboratoryResource
    {
        $laboratory = Laboratory::findOrFail($id);
        $user = User::findOrFail($laboratory->user->id);

        try{
            DB::beginTransaction();

            $user->update([
                'name' => !is_null($request->input('fullName')) && $request->input('fullName') !==""
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
                    : $user->password ,
            ]);

            $laboratory->update([
                'title' => !is_null($request->input('labName')) && $request->input('labName') !== ""
                    ? $request->input('labName')
                    : $laboratory->title,
                'address' => !is_null($request->input('address')) && $request->input('address') !== ""
                    ? $request->input('address')
                    :$laboratory->address ,
                'description' => !is_null($request->input('description')) && $request->input('description') !== ""
                    ? $request->input('description')
                    : $laboratory->description,
            ]);

            DB::commit();

            return new LaboratoryResource($laboratory);
        }catch (\Exception $e){
            DB::rollBack();

            \Log::info($e);
            return response()->json(['error' => 'Error creating laboratory.'], 500);
        }
    }

    public function updateMedia(UpdateLaboratoryRequest $request ,string $id): LaboratoryResource | JsonResponse
    {
        $laboratory = Laboratory::findOrFail($id);
        try{
            DB::beginTransaction();

            $avatarPath = null;
            $footerPath = null;
            $headerPath = null;
            $signaturePath = null;
            if($request->hasFile('avatar')){
                !is_null($laboratory->media->avatar)
                && Storage::disk('public')->delete($laboratory->media->avatar);
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }
            if($request->hasFile('header')){
                !is_null($laboratory->media->header)
                && Storage::disk('public')->delete($laboratory->media->header);
                $headerPath = $request->file('header')->store('headers', 'public');
            }
            if($request->hasFile('signature')){
                !is_null($laboratory->media->signature)
                && Storage::disk('public')->delete($laboratory->media->signature);
                $signaturePath = $request->file('signature')->store('signatures', 'public');
            }
            if($request->hasFile('footer')){
                !is_null($laboratory->media->footer)
                && Storage::disk('public')->delete($laboratory->media->footer);
                $footerPath = $request->file('footer')->store('footers', 'public');
            }

            $laboratory->media->update([
                'avatar' => $avatarPath ?:  $laboratory->media->avatar,
                'header' => $headerPath ?:  $laboratory->media->header,
                'signature' => $signaturePath ?:  $laboratory->media->signature,
                'footer' => $footerPath ?:  $laboratory->media->footer,
            ]);

            DB::commit();

            return new LaboratoryResource($laboratory);
        }catch (\Exception $e){
            DB::rollBack();

            \Log::info($e);
            return response()->json(['error' => 'Error creating laboratory.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) : Response
    {
        $laboratory = Laboratory::findOrFail($id);
        try{

            if($laboratory->media){
                if($laboratory->media->avatar){
                    Storage::disk('public')->delete($laboratory->media->avatar);
                }
                if($laboratory->media->header){
                    Storage::disk('public')->delete($laboratory->media->header);
                }
                if($laboratory->media->footer){
                    Storage::disk('public')->delete($laboratory->media->footer);
                }
                if($laboratory->media->signature){
                    Storage::disk('public')->delete($laboratory->media->signature);
                }
            }

//by deleting the user related laboratory will be deleted because of cascade relationship
// then the media row related to the laboratory will be deleted
            $laboratory->user->delete();

            return response(['success'], 200);
        }catch (\Exception $e){
            return response(['error'=> $e ], 200);
        }
    }
}
