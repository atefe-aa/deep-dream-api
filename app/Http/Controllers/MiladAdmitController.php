<?php

namespace App\Http\Controllers;

use App\Helpers\AgeHelper;
use App\Services\MiladService;
use Illuminate\Http\JsonResponse;
use function response;

class MiladAdmitController extends Controller
{

    private MiladService $miladService;

    public function __construct(MiladService $miladService)
    {
        $this->miladService = $miladService;
    }

    public function miladAdmit($admitNumber): JsonResponse
    {
        $admitInfoRes = $this->miladService->getAdmitData($admitNumber);

        if (isset($admitInfoRes['errors'])) {
            return response()->json(['errors' => 'Admit number not found'], 404);
        }

        $admitInfo = $admitInfoRes['data'][0];
        $age = AgeHelper::formatAge($admitInfo['ageYear'], $admitInfo['ageMonth'], $admitInfo['ageDay']);

        return response()->json([
            'data' => [
                "admitNumber" => $admitNumber,
                "name" => $admitInfo['firstName'] . " " . $admitInfo['lastName'],
                "nationalId" => $admitInfo['nationalId'],
                "age" => $age['age'],
                "ageUnit" => $age['ageUnit'],
                "gender" => $admitInfo['gender'],
                "doctorName" => $admitInfo['doctorFirstName'] . " " . $admitInfo['doctorLastName'],
                "description" => $admitInfo['description'],
            ]]);
    }
}
