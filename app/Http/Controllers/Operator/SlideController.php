<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\slide\StoreSlideRequest;
use App\Http\Resources\SlideResource;
use App\Models\Slide;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $slides = Slide::all();
            return SlideResource::collection($slides);
        } catch (Exception $e) {
            Log::info('Failed to retrieve slides: ' . $e->getMessage());
            return response()->json(['errors' => 'Failed to retrieve slides. Try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSlideRequest $request)
    {
        try {
            Slide::create($request->all());
            return response()->json(['success']);
        } catch (Exception $e) {
            Log::info('Failed to create slide : ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['errors' => 'Creating slide failed. Please try again later.'], 500);

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
    public function destroy(string $id): JsonResponse
    {
        $slide = Slide::findOrFail($id);
        try {
            $slide->delete();
            return response()->json(['success']);
        } catch (Exception $e) {
            Log::info('Failed to delete slide : ' . $e->getMessage(), ['slide id' => $id]);
            return response()->json(['errors' => 'Deleting slide failed. Please try again later.'], 500);
        }
    }
}
