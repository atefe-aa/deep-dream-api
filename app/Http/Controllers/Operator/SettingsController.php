<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\setting\UpdateSettingRequest;
use App\Http\Resources\SettingCategoryResource;
use App\Models\Setting;
use App\Models\SettingsCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use function response;

class SettingsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $settings = SettingsCategory::with('settings')->get();
        return SettingCategoryResource::collection($settings);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */

    public function update(Request $request, string $id): JsonResponse
    {
        $setting = Setting::findOrFail($id);
        if ($request->has('value') && is_numeric($request->input('value'))) {
            try {
                $setting->update([
                    'value' => $request->input('value')
                ]);
                return response()->json(['success' => 'Setting updated successfully.']);
            } catch (Exception $e) {
                Log::info('Failed to update setting: ' . $e->getMessage(), ['request' => $request->all()]);

                return response()->json(['errors' => 'Updating setting failed. Please try again later.'], 500);
            }
        }
        return response()->json(['errors' => 'invalid setting value.'], 500);
    }
}
