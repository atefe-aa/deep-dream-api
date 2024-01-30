<?php
namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class CytomineAuthService
{

    public function login(string $username, string $password) : string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(env('CYTOMINE_AUTH_URL'), [
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
            $authToken = $this->login(env('CYTOMINE_ADMIN_USERNAME'), env('CYTOMINE_ADMIN_PASSWORD'));
            if ($authToken) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $authToken
                ])->get(env('CYTOMINE_API_URL') . '/token.json', [
                    'username' => $username,
                    'validity' => true
                ]);

                if ($response->successful()) {
                    return ['token' => $response->json(['token'])];
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
                ])->post(env('CYTOMINE_API_URL') . '/user.json', [
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
