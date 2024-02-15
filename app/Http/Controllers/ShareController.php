<?php

namespace App\Http\Controllers;

use App\Http\Requests\share\ShareRequest;
use App\Models\Counsellor;
use App\Models\Test;
use App\Services\CytomineAuthService;
use App\Services\CytomineProjectService;
use App\Services\YunService;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function __construct(YunService $yunService, CytomineProjectService $cytomineProjectService, CytomineAuthService $cytomineAuthService)
    {
        $this->yunService = $yunService;
        $this->cytomineProjectService = $cytomineProjectService;
        $this->cytomineAuthService = $cytomineAuthService;
        $this->coreUrl = config('services.cytomine.core_url');
    }

    public function share(ShareRequest $request)
    {
        $user = Auth::user();
        if ($user) {
            $username = $user->hasRole(['superAdmin', 'operator'])
                ? env('CYTOMINE_ADMIN_USERNAME')
                : $user->username;

            $testId = $request->get('testId');
            $test = Test::with('scans.regions')->find($testId);

            if (!$test) {
                return response()->json(['message' => 'Test not found'], 404);
            }

            $counsellors = Counsellor::whereIn('id', $request->get('counsellors'))->get();

            $cytomineUsers = $counsellors->pluck('cytomine_user_id')->toArray();

            $result = $this->cytomineProjectService->addMemberToProject($cytomineUsers, $test->project_id, $username);
            if (isset($result['data'])) {
                $images = $test->scans->flatMap(function ($scan) {
                    return $scan->regions->pluck('cytomine_image_id');
                })->all();
                foreach ($counsellors as $counsellor) {
                    $username = $counsellor->phone . $counsellor->id;
                    $userTokenResponse = $this->cytomineAuthService->getUserToken($username);
                    $tokenKey = $userTokenResponse['token'] ?? null;
                    $link = $this->coreUrl
                        . '/#/project/' . $test->project_id
                        . '/image/' . $images[0]
                        . '?username=' . $username
                        . '&tokenKey=' . $tokenKey;

                    $shortenLinkRes = $this->yunService->shortUrl('image-view', $link);
                    $shortenLink = $shortenLinkRes['data'] ? $shortenLinkRes['data']['doc']['url'] : null;
                }


                return response()->json(['data' => $images]);
            }
            if (isset($result['errors'])) {
                return response()->json(['errors' => $result['errors']], 500);
            }
        }
        return response()->json(['message' => 'Not Authorized'], 403);
    }

    public function testShortenLink(Request $request)
    {
        $shortenLink = $this->yunService->shortUrl($request->get('title'), $request->get('url'));
        return $shortenLink['data']['doc']['url'];
    }

}
