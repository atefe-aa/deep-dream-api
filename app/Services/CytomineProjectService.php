<?php

namespace App\Services;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


/**
 * Service class for handling project-related operations in Cytomine.
 */
class CytomineProjectService
{
    protected CytomineAuthService $cytomineAuthService;
    private mixed $apiUrl;
    private mixed $coreUrl;
    private mixed $ontology;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $adminUsername;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $adminPassword;

    /**
     * Constructor for CytomineProjectService.
     *
     * @param CytomineAuthService $cytomineAuthService The auth service instance.
     */
    public function __construct(CytomineAuthService $cytomineAuthService)
    {
        $this->cytomineAuthService = $cytomineAuthService;

        $this->apiUrl = config('services.cytomine.api_url');
        $this->coreUrl = config('services.cytomine.core_url');
        $this->ontology = config('services.cytomine.ontology');
        $this->adminUsername = config('services.cytomine.admin_username');
        $this->adminPassword = config('services.cytomine.admin_password');
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

                return ['errors' => $response->json()];
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

    public function addMemberToProject(array $users, int $projectId, string $username): array
    {
        try {
            $authTokenResponse = $this->cytomineAuthService->loginWithToken($username);
            $usersQueryString = implode(',', $users);
            if ($authTokenResponse && !empty($authTokenResponse['token'])) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authTokenResponse['token']
                ])->post($this->apiUrl . '/project/' . $projectId . '/user.json?users=' . $usersQueryString);

                if ($response->successful()) {
                    return ['data' => ['message' => 'success']];
                }

                return ['errors' => $response->json()];
            }
            return ['errors' => 'Authentication failed. No token received.'];

        } catch (Exception $e) {
            Log::error("Adding member to the project failed: {$e->getMessage()}", [
                'username' => $username,
                'users' => $users,
                'projectId' => $projectId
            ]);
            return ['errors' => 'Failed to add member to the project. Please try again later.'];
        }
    }

    public function uploadImage(int $projectId, string $imagePath, string $username): array
    {
        $multipartData = [
            [
                'name' => 'file',
                'contents' => fopen($imagePath, 'rb'),
                'filename' => basename($imagePath)
            ],
        ];
        try {
            $authTokenResponse = $this->cytomineAuthService->login($this->adminUsername, $this->adminPassword);

            if ($authTokenResponse && !empty($authTokenResponse['data'])) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authTokenResponse['data']['token']
                ])->post($this->coreUrl . '/upload?cytomine=' . $this->coreUrl . '&idStorage=51&idProject=' . $projectId, [
                    'multipart' => $multipartData
                ]);

                if ($response->successful()) {
                    return ['data' => ['projectId' => $response['project']['id']]];
                }

                return ['errors' => $response->json()];
            }
            return ['errors' => 'Authentication failed. No token received.'];

        } catch (Exception $e) {
            Log::error("Uploading image failed: {$e->getMessage()}", [
                'username' => $username,
                'projectId' => $projectId,
                'image' => $imagePath
            ]);
            return ['errors' => 'Failed to upload the image. Please try again later.'];
        }
    }


}
