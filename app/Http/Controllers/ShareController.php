<?php

namespace App\Http\Controllers;

use App\Http\Requests\share\ShareRequest;
use App\Models\Counsellor;
use App\Models\Test;

class ShareController extends Controller
{
    public function share(ShareRequest $request)
    {
        $test = Test::where('id', $request->get('testId'))->first();
        $projectId = $test->project_id;

        $counsellors = Counsellor::whereIn('id', $request->get('counsellors'))->get();
        $cytomineUsers = [];
        foreach ($counsellors as $counsellor) {
            $cytomineUsers[] = $counsellor->cytomine_user_id;
        }

        return response(['projectId' => $projectId, 'cytomineUsers' => $cytomineUsers]);
    }
}
