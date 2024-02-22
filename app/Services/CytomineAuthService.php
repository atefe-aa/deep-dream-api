<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


/**
 * Service class for handling authentication with the Cytomine API.
 */
class CytomineAuthService
{
    private mixed $adminUsername;
    private mixed $adminPassword;
    private mixed $authUrl;
    private mixed $apiUrl;
    private mixed $coreUrl;


    /**
     * Constructor for CytomineAuthService.
     */
    public function __construct()
    {
        $this->adminUsername = config('services.cytomine.admin_username');
        $this->adminPassword = config('services.cytomine.admin_password');
        $this->authUrl = config('services.cytomine.auth_url');
        $this->apiUrl = config('services.cytomine.api_url');
        $this->coreUrl = config('services.cytomine.core_url');
    }

    /**
     * Logs in with a token for a specific user.
     *
     * @param string $username The username for login.
     * @return array The response array containing the token or error.
     */
    public function loginWithToken(string $username): array
    {
        try {
            $tokenKey = $this->getUserToken($username);

            if ($tokenKey['token']) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                ])->get($this->coreUrl . '/login/loginWithToken?username=' . $username . '&tokenKey=' . $tokenKey['token']);
                if ($response->successful()) {
                    return ['token' => $response->json('token')];
                }

                return ['error' => $response->status()];
            }
            return ['error' => 'Something went wrong during authentication.'];

        } catch (Exception $e) {
            Log::error("Logging in the user with token Failed: {$e->getMessage()}", [
                'username' => $username,
            ]);
            return ['errors' => 'Failed to login the user. Please try again later.'];
        }
    }

    /**
     * Retrieves a user token for a given username.
     *
     * @param string $username The username for which to retrieve the token.
     * @return array|null An array with the token or null on failure.
     */
    public function getUserToken(string $username): ?array
    {
        try {
            $authToken = $this->login($this->adminUsername, $this->adminPassword);

            if (isset($authToken['data'])) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authToken['data']['token']
                ])->get($this->apiUrl . '/token.json', [
                    'username' => $username,
                    'validity' => true
                ]);

                $responseData = $response->json();

                if (isset($responseData['success']) && $responseData['success']) {
                    return ['success' => true, 'token' => $responseData['token']];
                }
                return ['success' => false, 'error' => $responseData['errorValues'] ?? 'Unknown error'];
            }
            return ['success' => false, 'error' => 'Authentication failed.'];
        } catch (Exception $e) {
            Log::error("Getting user token Failed: {$e->getMessage()}", [
                'username' => $username,
            ]);
            return ['success' => false, 'error' => 'Failed to get the user token. Please try again later.'];
        }
    }

    /**
     * Login to Cytomine with provided credentials.
     *
     * @param string $username The username for login.
     * @param string $password The password for login.
     * @return array[] The authentication token or status code on failure.
     */
    public function login(string $username, string $password): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->authUrl, [
                "username" => $username,
                "rememberMe" => true,
                "password" => $password
            ]);

            if ($response->successful()) {
                return ['data' => ['token' => $response['token']]];
            }
            return ['errors' => $response->status()];
        } catch (Exception $e) {
            Log::error("Authentication Failed: {$e->getMessage()}", [
                'username' => $username,
            ]);
            return ['errors' => 'Failed to log in. Please try again later.'];
        }

    }

    /**
     * Registers a new user with Cytomine.
     *
     * @param array $data The user data for registration.
     * @return array|null The response array on success, or null on failure.
     */
    public function registerUser(array $data): ?array
    {

        try {
            $authToken = $this->login(env('CYTOMINE_ADMIN_USERNAME'), env('CYTOMINE_ADMIN_PASSWORD'));
            if (isset($authToken['data'])) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authToken['data']['token']
                ])->post($this->apiUrl . '/user.json', [
                    'firstname' => explode(" ", $data['name'])[0],
                    'lastname' => explode(" ", $data['name'])[1] ?? "-",
                    'language' => 'EN',
                    'email' => $data['username'] . '@gmail.com',
                    'username' => $data['username'],
                    'password' => $data['password'],
                ]);

                if ($response->successful()) {

                    $responseData = $response->json();
                    $defineRoleResponse = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $authToken['data']['token']
                    ])->put(env('CYTOMINE_API_URL') . '/user/' . $responseData['user']['id'] . '/role/44/define.json');

                    return $responseData;
                }

                return ['errors' => $response->body()];
            }
            return ['errors' => 'Something went wrong during authentication.'];

        } catch (Exception $e) {
            Log::error("Cytomine User Creation Failed: {$e->getMessage()}", [
                'name' => $data['name'],
            ]);
            return ['errors' => 'Failed to create user. Please try again later.'];
        }
    }
}
