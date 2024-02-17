<?php

namespace App\Http\Controllers;

use App\Http\Requests\share\ShareRequest;
use App\Models\Counsellor;
use App\Models\Test;
use App\Services\CytomineAuthService;
use App\Services\CytomineProjectService;
use App\Services\SmsService;
use App\Services\YunService;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JsonException;
use PHPUnit\Framework\Exception;

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
    private mixed $coreUrl;
    private YunService $yunService;
    private SmsService $smsService;

    public function __construct(SmsService $smsService, YunService $yunService, CytomineProjectService $cytomineProjectService, CytomineAuthService $cytomineAuthService)
    {
        $this->yunService = $yunService;
        $this->smsService = $smsService;
        $this->cytomineProjectService = $cytomineProjectService;
        $this->cytomineAuthService = $cytomineAuthService;
        $this->coreUrl = config('services.cytomine.core_url');
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
            if ($tokenKey) {
                $link = $this->coreUrl
                    . '/#/project/' . $test->project_id
                    . '/image/' . $images[0]
                    . '?username=' . $username
                    . '&token=' . $tokenKey;
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
            ? env('CYTOMINE_ADMIN_USERNAME')
            : $user->username;
    }

    private function validateTest($testId): Model|Collection|Builder|array|null
    {
        return Test::with('scans.regions')->find($testId);
    }


    public function sendSMS(string $recipient, string $senderName, string $link): array
    {
        try {
            $shortenLinkRes = $this->yunService->shortUrl('image-view', $link);
            $shortenLink = $shortenLinkRes['data'] ? $shortenLinkRes['data']['doc']['url'] : null;

            $message = $senderName
                . " " . "has asked you to collaborate on a test.View the image via this link: \n"
                . $shortenLink;

            $requestBody = [
                "recipient" => [$recipient],
                "sender" => env('SMS_PANEL_SENDER_NUMBER'),
                "time" => now('UTC')->addSeconds(30),
                "message" => $message
            ];
            $smsRes = $this->smsService->sendNormal($requestBody);
            if (isset($smsRes['errors'])) {
                throw new Exception($smsRes['errors']);
            }
            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Failed to send SMS to ' . $recipient . ': ' . $e->getMessage());
            // Return a failure status and the recipient identifier
            return ['success' => false, 'recipient' => $recipient,];
        }

    }

    public function testShortenLink()
    {
        return now('UTC');
    }
}
