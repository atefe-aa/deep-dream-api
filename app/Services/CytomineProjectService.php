<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Log;

/**
 * Service class for handling project-related operations in Cytomine.
 */
class CytomineProjectService
{
    protected CytomineAuthService $cytomineAuthService;
    private mixed $apiUrl;
    private mixed $ontology;

    /**
     * Constructor for CytomineProjectService.
     *
     * @param CytomineAuthService $cytomineAuthService The auth service instance.
     */
    public function __construct(CytomineAuthService $cytomineAuthService)
    {
        $this->cytomineAuthService = $cytomineAuthService;

        $this->apiUrl = config('services.cytomine.api_url');
        $this->ontology = config('services.cytomine.ontology');
    }

    /**
     * Creates a new project in Cytomine.
     *
     * @param string $projectName The name of the project to be created.
     * @param string $username The username for whom to create the project.
     * @return array An array containing the project ID on success or errors on failure.
     */
    public function createProject(string $projectName, string $username): array
    {
        try {
            $authTokenResponse = $this->cytomineAuthService->loginWithToken($username);

            if ($authTokenResponse && !empty($authTokenResponse['token'])) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authTokenResponse['token']
                ])->post($this->apiUrl . '/project.json', [
                    "name" => $projectName,
                    "ontology" => $this->ontology
                ]);

                if ($response->successful()) {
                    return ['data' => ['projectId' => $response['project']['id']]];
                }

                return ['errors' => $response];
            }
            return ['errors' => 'Authentication failed. No token received.'];

        } catch (Exception $e) {
            Log::error("Cytomine Project Creation Failed: {$e->getMessage()}", [
                'username' => $username,
                'projectName' => $projectName
            ]);
            return ['errors' => 'Failed to create project. Please try again later.'];
        }
    }
}
