<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class CytomineAuthService
{
    private mixed $adminUsername;
    private mixed $adminPassword;
    private mixed $authUrl;
    private mixed $apiUrl;
    private mixed $coreUrl;

    public function __construct() {
        $this->adminUsername = config('services.cytomine.admin_username');
        $this->adminPassword = config('services.cytomine.admin_password');
        $this->authUrl = config('services.cytomine.auth_url');
        $this->apiUrl = config('services.cytomine.api_url');
        $this->coreUrl = config('services.cytomine.core_url');
    }

    public function login(string $username, string $password) : string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->authUrl, [
            "username" => $username,
            "rememberMe" => true,
            "password" => $password
        ]);

        if ($response->successful()) {
            return $response['token'];
        }
        return $response->status();
    }

    public function getUserToken(string $username): ?array
    {
        try {
            $authToken = $this->login($this->adminUsername, $this->adminPassword);

            if (is_string($authToken)) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authToken
                ])->get($this->apiUrl . '/token.json', [
                    'username' => $username,
                    'validity' => true
                ]);

                $responseData = $response->json();

                if (isset($responseData['success']) && $responseData['success']) {
                    return ['token' => $responseData['token']];
                }
                return ['error' => $responseData['error'] ?? 'Unknown error'];
            }
            return ['error' => 'Authentication failed.'];
        } catch (\Exception $e) {
            \Log::error("Exception in getUserToken: " . $e->getMessage());
            return ['error' => 'Exception occurred.'];
        }
    }

    public function loginWithToken($username): array
    {
        try {
            $tokenKey = $this->getUserToken($username);

            if ($tokenKey['token']) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                ])->get($this->coreUrl . '/login/loginWithToken?username='.$username.'&tokenKey='.$tokenKey['token']);
                if ($response->successful()) {
                    return ['token' => $response->json('token')];
                }

                return ['error' => $response->status()];
            }
            return ['error' => 'Something went wrong during authentication.'];

        } catch (\Exception $e) {
            // Log or handle the exception
            return ['error' => $e->getMessage()];
        }
    }

    public function registerUser($data): ?array
    {
        try {
            $authToken = $this->login(env('CYTOMINE_ADMIN_USERNAME'), env('CYTOMINE_ADMIN_PASSWORD'));
            if ($authToken) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authToken
                ])->post($this->apiUrl . '/user.json', [
                    'firstname'=>explode(" ",$data['name'])[0],
                    'lastname'=>explode(" ",$data['name'])[1],
                    'language'=>'EN',
                    'email' => $data['username'].'@gmail.com',
                    'username'=>$data['username'],
                    'password'=>$data['password'],
                ]);

                if ($response->successful()) {
                    $defineRoleResponse = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authToken
                ])->put(env('CYTOMINE_API_URL') . '/user/'.$response['user']['id'].'/role/44/define.json');

                    return ['success' => $response->json()];
                }

                return ['error' => $response->status()];
            }
            return ['error' => 'Something went wrong during authentication.'];

        } catch (\Exception $e) {
            // Log or handle the exception
            return ['error' => $e->getMessage()];
        }
    }
}
