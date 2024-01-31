<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\price\StorePriceRequest;
use App\Http\Resources\PriceResource;
use App\Models\Price;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            return new PriceResource($price);
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
