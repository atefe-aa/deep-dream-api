<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLaboratoryRequest;
use App\Http\Resources\LaboratoryResource;
use App\Models\Laboratory;
use App\Models\LaboratoryMedia;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LaboratoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return LaboratoryResource::collection(Laboratory::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLaboratoryRequest $request): LaboratoryResource | JsonResponse
    {
        try{
            $user = User::create([
                'name' => $request->input('fullName'),
                'username' => $request->input('username'),
                'phone' => $request->input('phone'),
                'password' => $request->input('password'),
            ]);

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

            return new LaboratoryResource($laboratory);
        }catch (\Exception $e){
            \Log::info($e);
            return response()->json(['error' => 'Error creating laboratory.'], 500);
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
