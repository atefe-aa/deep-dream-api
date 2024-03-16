<?php

namespace App\Http\Controllers;

use App\Http\Requests\share\ShareRequest;
use App\Models\Counsellor;
use App\Models\Link;
use App\Models\Test;
use App\Services\CytomineAuthService;
use App\Services\CytomineProjectService;
use App\Services\SmsService;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JsonException;
use RuntimeException;

class ShareController extends Controller
{

    /**
     * @var CytomineProjectService
     */
    private CytomineProjectService $cytomineProjectService;
    private CytomineAuthService $cytomineAuthService;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private SmsService $smsService;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $frontUrl;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $frontUri;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $cytomineAdminUsername;

    public function __construct(SmsService $smsService, CytomineProjectService $cytomineProjectService, CytomineAuthService $cytomineAuthService)
    {
        $this->smsService = $smsService;
        $this->cytomineProjectService = $cytomineProjectService;
        $this->cytomineAuthService = $cytomineAuthService;
        $this->cytomineAdminUsername = config('cytomine.admin_username');
        $this->frontUrl = config('services.front.url');
        $this->frontUri = config('services.front.uri');
    }

    /**
     * @throws JsonException
     */
    public function share(ShareRequest $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Not Authorized'], 403);
        }

        $username = $this->determineUsername($user);
        $test = $this->validateTest($request->get('testId'));
        if (!$test) {
            return response()->json(['message' => 'Test not found'], 404);
        }

        $counsellors = Counsellor::whereIn('id', $request->get('counsellors'))->get();
        $authorizedCounsellors = $user->hasRole(["superAdmin", 'operator']) ? $counsellors :
            $counsellors->filter(function ($counsellor) use ($user) {
                return $counsellor->lab_id === $user->laboratory->id;
            });

        if ($authorizedCounsellors->isEmpty()) {
            return response()->json(['message' => 'You are not authorized to send SMS to the selected counsellors.'], 403);
        }
        $cytomineUsers = $authorizedCounsellors->pluck('cytomine_user_id')->toArray();

        if (!$test->project_id) {
            return response()->json(['message' => 'No Project has been set for this test.'], 404);
        }

        $result = $this->cytomineProjectService
            ->addMemberToProject($cytomineUsers, $test->project_id, $username);

        if (isset($result['errors'])) {
            Log::info('Cytomine add member failed: ', $result['errors']);
            return response()->json([
                'message' => 'Failed to add members to the project.',
            ], 500);
        }

        $images = $test->scans->flatMap(function ($scan) {
            return $scan->regions->pluck('cytomine_image_id');
        })->all();

        $failedCounsellors = [];
        foreach ($authorizedCounsellors as $counsellor) {
            $username = $counsellor->phone . $counsellor->id;
            $userTokenResponse = $this->cytomineAuthService->getUserToken($username);
            $tokenKey = $userTokenResponse['token'] ?? null;
            Log::info($userTokenResponse);
            if ($tokenKey) {
                $link = "/image?username=$username&project=$test->project_id&image=$images[0]&token=$tokenKey";
                $sendSmsResult = $this->sendSMS($counsellor->phone, $user->name, $link);
                if (!$sendSmsResult['success']) {
                    $failedCounsellors[] = [
                        'counsellorId' => $counsellor->id,
                        'counsellorName' => $counsellor->name,
                    ];
                }
            }
        }
        if (count($failedCounsellors) > 0) {
            return response()->json([
                'message' => 'Some counsellors did not receive the SMS',
                'errors' => $failedCounsellors
            ], 500);
        }
        // Respond with success if all SMSs were sent successfully
        return response()->json(['message' => 'SMS sent to all counsellors successfully']);

    }

    private function determineUsername($user): string
    {
        return $user->hasRole(['superAdmin', 'operator'])
            ? $this->cytomineAdminUsername
            : $user->username;
    }

    private function validateTest($testId): Model|Collection|Builder|array|null
    {
        return Test::with('scans.regions')->find($testId);
    }


    public function sendSMS(string $recipient, string $senderName, string $link): array
    {
        try {
            $shortenLinkRes = $this->shortenLink($link);
            $shortenLink = $shortenLinkRes ?: null;

            $message = $senderName
                . " " . "has requested you to collaborate on this slide image via this link: \n"
                . $shortenLink;

            $requestBody = [
                "recipient" => [$recipient],
                "sender" => env('SMS_PANEL_SENDER_NUMBER'),
                "time" => now('UTC')->addSeconds(30),
                "message" => $message
            ];

            $smsRes = $this->smsService->sendNormal($requestBody);
            if (isset($smsRes['errors'])) {
                throw new RuntimeException($smsRes['errors']);
            }
            return ['success' => true];
        } catch (Exception $e) {
            Log::error('Failed to send SMS to ' . $recipient . ': ' . $e->getMessage());
            // Return a failure status and the recipient identifier
            return ['success' => false, 'recipient' => $recipient,];
        }

    }

    public function shortenLink(string $link): ?string
    {
        $code = $this->generateUniqueCode();
        $link = Link::create([
            'uri' => $link,
            'code' => $code,
        ]);
        if (!$link) {
            return null;
        }
        return $this->frontUrl . $this->frontUri . '/' . $link->code;
    }

    public function generateUniqueCode($length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $uniqueCode = '';
        for ($i = 0; $i < $length; $i++) {
            $uniqueCode .= $characters[random_int(0, $charactersLength - 1)];
        }
        $linkWithTheSameCode = Link::where('code', $uniqueCode)->first();

        while ($linkWithTheSameCode) {
            $uniqueCode = $this->generateUniqueCode($length);
        }
        return $uniqueCode;
    }
}
