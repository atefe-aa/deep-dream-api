<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\slide\StoreSlideRequest;
use App\Http\Requests\slide\UpdateSlideRequest;
use App\Http\Resources\SlideResource;
use App\Models\Slide;
use Exception;
use Illuminate\Http\JsonResponse;
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
            $slides = Slide::orderBy('nth')->get();
            return SlideResource::collection($slides);
        } catch (Exception $e) {
            Log::info('Failed to retrieve slides: ' . $e->getMessage());
            return response()->json(['errors' => 'Failed to retrieve slides. Try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSlideRequest $request): JsonResponse
    {
        try {
            Slide::create(
                [
                    "nth" => $request->get('nth'),
                    "sw_y" => $request->get('coordinates')['sw_y'],
                    "sw_x" => $request->get('coordinates')['sw_x'],
                    "ne_x" => $request->get('coordinates')['ne_x'],
                    "ne_y" => $request->get('coordinates')['ne_y'],
                ]
            );
            return response()->json(['data' => 'success']);
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
    public function update(UpdateSlideRequest $request, string $id)
    {
        $slide = Slide::findOrFail($id);
        try {
            $slide->update($request->all());
            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Failed to update slide : ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['errors' => 'Updating slide failed. Please try again later.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $slide = Slide::findOrFail($id);
        try {
            $slide->delete();
            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Failed to delete slide : ' . $e->getMessage(), ['slide id' => $id]);
            return response()->json(['errors' => 'Deleting slide failed. Please try again later.'], 500);
        }
    }
}
