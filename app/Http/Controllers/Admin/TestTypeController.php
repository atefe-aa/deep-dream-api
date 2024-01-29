<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestTypeResource;
use App\Models\TestType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TestTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : AnonymousResourceCollection
    {
        $query = TestType::query();

        if ($request->has('laboratory')) {
            $labId = $request->input('laboratory'); // Corrected line
            $query->whereHas('prices', function ($query) use ($labId) {
                $query->where('lab_id', $labId);
            });
        }

        $testTypes = $query->get();

        return TestTypeResource::collection($testTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
