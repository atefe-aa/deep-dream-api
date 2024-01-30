<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class CytomineProjectService
{
    protected CytomineAuthService $cytomineAuthService;

    public function __construct(CytomineAuthService $cytomineAuthService)
    {
        $this->cytomineAuthService = $cytomineAuthService;
    }
    public function createProject(string $projectName,string $username): array
    {
        try {
            $authTokenResponse = $this->cytomineAuthService->loginWithToken($username);

            if ($authTokenResponse) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authTokenResponse['token']
                ])->post(env('CYTOMINE_API_URL') . '/project.json', [
                    "name"=> $projectName,
                    "ontology"=> 235359
                ]);

                if ($response->successful()) {
                    return ['projectId' => $response['project']['id']];
                }

                return ['errors' => $response];
            }
            return ['errors' => 'Something went wrong logging in.'];

        } catch (\Exception $e) {
            // Log or handle the exception
            return ['errors' => $e];
        }
    }
}
