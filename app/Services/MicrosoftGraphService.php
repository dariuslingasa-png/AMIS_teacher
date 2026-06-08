<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftGraphService
{
    private string $tenantId;
    private string $clientId;
    private string $clientSecret;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->tenantId     = (string) config('services.microsoft.tenant_id');
        $this->clientId     = (string) config('services.microsoft.client_id');
        $this->clientSecret = (string) config('services.microsoft.client_secret');
    }

    /**
     * Get Client Credentials access token.
     * 
     * @throws \Exception
     */
    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = Http::asForm()->post(
            "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
            [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => 'https://graph.microsoft.com/.default',
            ]
        );

        if (!$response->successful()) {
            Log::error('Microsoft Graph token retrieval failed', $response->json() ?: []);
            throw new \Exception('Failed to get Microsoft access token: ' . $response->body());
        }

        $this->accessToken = (string) $response->json('access_token');
        return $this->accessToken;
    }

    /**
     * Create graph request client.
     */
    private function graph(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withToken($this->getAccessToken())
                   ->baseUrl('https://graph.microsoft.com/v1.0')
                   ->timeout(60);
    }

    /**
     * Reset the Microsoft account password for a teacher.
     * 
     * @throws \Exception
     */
    public function resetPassword(string $upnOrId, string $newPassword, bool $forceChangePasswordNextSignIn = true): void
    {
        $response = $this->graph()->patch("/users/" . urlencode($upnOrId), [
            'passwordProfile' => [
                'password'                      => $newPassword,
                'forceChangePasswordNextSignIn' => $forceChangePasswordNextSignIn,
            ],
        ]);

        if (!$response->successful()) {
            Log::error("Failed to reset password for user {$upnOrId}", $response->json() ?: []);
            throw new \Exception('Failed to reset password: ' . $response->body());
        }
    }
}
