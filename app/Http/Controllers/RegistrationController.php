<?php

namespace App\Http\Controllers;

use App\Http\Requests\registration\StoreRegistrationRequest;
use App\Http\Resources\RegistrationResource;
use App\Models\Patient;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) :AnonymousResourceCollection
    {

        $query = Test::with(['testType','laboratory','patient']);

        if($request->has('search'))
        {
            $searchTerm = $request->get('search');

            $query->where(function ($query) use ($searchTerm) {
               $query->where('sender_register_code', 'like', '%'.$searchTerm.'%')
                   ->orWhere('doctor_name', 'like', '%'.$searchTerm.'%')
                   ->orWhere('status', 'like', '%'.$searchTerm.'%');
            })
            ->orWhereHas('testType', function ($query) use ($searchTerm) {
                $query->where('title', 'like','%'.$searchTerm.'%');
            })
                ->orWhereHas('patient', function ($query) use ($searchTerm) {
                    $query->where('name', 'like','%'.$searchTerm.'%')
                        ->orWhere('national_id', 'like','%'.$searchTerm.'%');
                })
                ->orWhereHas('laboratory', function ($query) use ($searchTerm) {
                    $query->where('title', 'like','%'.$searchTerm.'%');
                });
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        switch($sortBy){
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
            case 'registration':
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
    public function store(StoreRegistrationRequest $request)
    {
        try{
            DB::beginTransaction();

            //to prevent duplicate rows for a patient
            $patient = Patient::where('national_id',$request->input('nationalId'))->first();
            if($patient){
                $patient->update([
                    'age' => $request->input('age'),
                    'ageUnit' => $request->input('ageUnit'),
                ]);
            }else{
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
                'lab_id' => $request->input('laboratoryId'),
                'sender_register_code' => $request->input('senderRegisterCode'),
                'test_type_id' => $request->input('testType'),
                'doctor_name' => $request->input('doctorName'),
                'num_slide' => $request->input('numberOfSlides'),
                'description' => $request->input('description'),
            ]);

           $test->setPriceAttribute();

            $test->save();

            DB::commit();

            return new RegistrationResource($test);

        }catch(\Exception $e){
            DB::rollBack();

            \Log::info($e);
            return response()->json(['error' => 'Error creating registration.'], 500);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $test = Test::findOrFail($id);

        return new RegistrationResource($test);
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
