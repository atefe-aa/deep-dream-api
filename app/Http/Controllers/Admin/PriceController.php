<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\price\StorePriceRequest;
use App\Http\Requests\price\UpdatePriceRequest;
use App\Http\Resources\PriceResource;
use App\Models\Price;
use Exception;
use Illuminate\Http\JsonResponse;
use Log;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePriceRequest $request
     * @return PriceResource|JsonResponse
     */
    public function store(StorePriceRequest $request): PriceResource|JsonResponse
    {
        try {
            $price = Price::create([
                'lab_id' => $request->input('labId'),
                'test_type_id' => $request->input('testType'),
                'price' => $request->input('price'),
                'price_per_slide' => $request->input('extraPrice'),
                'description' => $request->input('description'),
            ]);
            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Failed to create test type price: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['error' => 'Creating new test type price failed. Try again later.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)

    {
        $price = Price::findOrFail($id);
        $this->authorize('view', $price);
        return new PriceResource($price);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePriceRequest $request, string $id): JsonResponse
    {
        try {
            $price = Price::findOrFail($id);
            $price->update([
                'price' => $request->input('price', $price->price),
                'price_per_slide' => $request->input('extraPrice', $price->price_per_slide),
                'description' => $request->input('description'),
            ]);
            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            Log::info('Failed to update test type price: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['error' => 'Updating test type price failed. Try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $price = Price::findOrFail($id);
        $this->authorize('delete', $price);
        try {
            $price->delete();
            return response()->json(['data' => 'success']);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::info('Failed to delete price : ' . $e->getMessage(), ['price id' => $id]);
            return response()->json(['errors' => 'Deleting price failed. Please try again later.'], 500);
        }
    }
}
