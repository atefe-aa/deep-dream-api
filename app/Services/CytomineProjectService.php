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
            $authToken = $this->cytomineAuthService->getUserToken($username)['token'];

            if ($authToken) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authToken
                ])->post(env('CYTOMINE_API_URL') . '/project.json', [
                    "name"=> $projectName,
                    "ontology"=> 235359
                ]);

                if ($response->successful()) {
                    return ['projectId' => $response->json()['project']['id']];
                }

                return ['error' => $response->status()];
            }
            return ['error' => 'Something went wrong creating project.'];

        } catch (\Exception $e) {
            // Log or handle the exception
            return ['error' => $e->getMessage()];
        }
    }
}
