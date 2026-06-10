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
    private ?string $delegatedToken = null;

    public function __construct()
    {
        $this->tenantId     = (string) config('services.microsoft.tenant_id');
        $this->clientId     = (string) config('services.microsoft.client_id');
        $this->clientSecret = (string) config('services.microsoft.client_secret');
    }

    // ── Auth ──────────────────────────────────────────────────────────

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
     * Get a delegated access token using ROPC flow (on behalf of the admin user).
     * Requires "Allow public client flows" enabled on the app registration.
     * The admin account must NOT have MFA enabled.
     */
    private function getDelegatedToken(): string
    {
        if ($this->delegatedToken) {
            return $this->delegatedToken;
        }

        $adminUpn      = config('services.microsoft.admin_upn');
        $adminPassword = config('services.microsoft.admin_password');

        if (empty($adminPassword)) {
            throw new \Exception('MICROSOFT_ADMIN_PASSWORD is not set in .env');
        }

        $response = Http::asForm()->post(
            "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
            [
                'grant_type'    => 'password',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username'      => $adminUpn,
                'password'      => $adminPassword,
                'scope'         => 'https://graph.microsoft.com/.default',
            ]
        );

        if (!$response->successful()) {
            Log::error('Microsoft Graph ROPC token error', $response->json() ?: []);
            throw new \Exception('Failed to get delegated access token: ' . $response->body());
        }

        $this->delegatedToken = $response->json('access_token');
        Log::info("ROPC token obtained successfully for {$adminUpn}");
        return $this->delegatedToken;
    }

    private function graphDelegated(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withToken($this->getDelegatedToken())
                   ->baseUrl('https://graph.microsoft.com/v1.0')
                   ->timeout(60);
    }

    // ── User Management ───────────────────────────────────────────────

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

    // ── Teams Management ──────────────────────────────────────────────

    /**
     * Create a new Team with an owner (required in application context).
     */
    public function createTeam(string $displayName, string $description = ''): array
    {
        $adminUpn = config('services.microsoft.admin_upn');

        // First get the admin user's object ID
        $adminUser = $this->graph()->get("/users/{$adminUpn}")->json();
        $adminId   = $adminUser['id'] ?? null;

        if (!$adminId) {
            throw new \Exception("Could not find admin user: {$adminUpn}");
        }

        $response = $this->graph()->post('/teams', [
            'template@odata.bind' => "https://graph.microsoft.com/v1.0/teamsTemplates('standard')",
            'displayName'         => $displayName,
            'description'         => $description,
            'members'             => [
                [
                    '@odata.type'     => '#microsoft.graph.aadUserConversationMember',
                    'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('{$adminId}')",
                    'roles'           => ['owner'],
                ],
            ],
        ]);

        if (!$response->successful() && $response->status() !== 202) {
            Log::error('Graph createTeam error', $response->json() ?: []);
            throw new \Exception('Failed to create team: ' . $response->body());
        }

        // Team creation returns 202 with Location header
        // Format can be: /teams/{id}/operations/{opId}
        //             or: /teams('{id}')/operations('{opId}')
        $location = $response->header('Location') ?? '';
        preg_match("/teams\(?'?([0-9a-f\-]{36})'?\)?/i", $location, $matches);
        $teamId = $matches[1] ?? null;

        if (!$teamId) {
            throw new \Exception('Could not extract team ID from Location header: ' . $location);
        }

        return ['id' => $teamId, 'displayName' => $displayName];
    }

    /**
     * Get the Announcements channel ID of a team (falls back to General).
     */
    public function getGeneralChannelId(string $teamId): ?string
    {
        $response = $this->graph()->get("/teams/{$teamId}/channels");
        if (!$response->successful()) return null;

        $channels = $response->json('value', []);

        // Prefer Announcements, fall back to General
        foreach ($channels as $ch) {
            if (stripos($ch['displayName'], 'Announcement') !== false) return $ch['id'];
        }
        foreach ($channels as $ch) {
            if (stripos($ch['displayName'], 'General') !== false) return $ch['id'];
        }
        return $channels[0]['id'] ?? null;
    }

    /**
     * Post a welcome message to a channel.
     */
    public function postWelcomeCard(string $teamId, string $channelId, array $section): void
    {
        $grade   = $section['grade_level'];
        $mode    = $section['learning_mode'];
        $shift   = $section['shift'] ?? null;
        $gender  = ($section['gender'] ?? 'male') === 'male' ? 'Boys' : 'Girls';
        $subject = $section['subject'] ?? null;
        $teacher = $section['teacher'] ?? null;

        $modeLabel = str_contains($mode, 'Flexible') ? 'Flexible Online Learning' : 'Face-to-Face';
        $studentPortalUrl = (string) config('services.student_portal_url');

        if ($subject) {
            // Channel welcome — subject-specific
            $greetingTitle = "Assalamualaikum wa Rahmatullahi wa Barakatuh, {$grade} Students! Welcome to {$subject}!";
            $teacherRow = $teacher
                ? "<tr><td><b>Assigned Teacher</b></td><td>{$teacher}</td></tr>"
                : '<tr><td><b>Assigned Teacher</b></td><td>TBA</td></tr>';
            $shiftRow = $shift ? "<tr><td><b>Shift</b></td><td>{$shift}</td></tr>" : '';

            $html = "
<h2>{$greetingTitle}</h2>
<hr/>
<table>
  <tr><td><b>Grade</b></td><td>{$grade}</td></tr>
  <tr><td><b>Mode</b></td><td>{$modeLabel}</td></tr>
  {$shiftRow}
  <tr><td><b>Gender</b></td><td>{$gender}</td></tr>
  <tr><td><b>Subject</b></td><td>{$subject}</td></tr>
  {$teacherRow}
</table>
<hr/>
<p><b>Reminders:</b></p>
<ul>
  <li>Check announcements daily</li>
  <li>Join classes on time</li>
  <li>Use your school account (@amis.edu.ph)</li>
  <li>Be respectful in all channels</li>
  <li>Submit requirements on time</li>
</ul>
<p><a href=\"{$studentPortalUrl}\">Open Student Portal</a></p>
";
        } else {
            // Team General/Announcements welcome
            $shiftRow = $shift ? "<tr><td><b>Shift</b></td><td>{$shift}</td></tr>" : '';

            $html = "
<h2>Assalamu Alaikum wa Rahmatullahi wa Barakatuh</h2>
<hr/>
<table>
  <tr><td><b>Grade</b></td><td>{$grade}</td></tr>
  <tr><td><b>Mode</b></td><td>{$modeLabel}</td></tr>
  {$shiftRow}
  <tr><td><b>Gender</b></td><td>{$gender}</td></tr>
  <tr><td><b>Advisory</b></td><td>TBA</td></tr>
</table>
<hr/>
<p><b>Reminders:</b></p>
<ul>
  <li>Check announcements daily</li>
  <li>Join classes on time</li>
  <li>Use your school account (@amis.edu.ph)</li>
  <li>Be respectful in all channels</li>
  <li>Submit requirements on time</li>
</ul>
<p><a href=\"{$studentPortalUrl}\">Open Student Portal</a></p>
";
        }

        $payload = [
            'body' => [
                'contentType' => 'html',
                'content'     => $html,
            ],
        ];

        $response = $this->graphDelegated()->post("/teams/{$teamId}/channels/{$channelId}/messages", $payload);

        if (!$response->successful()) {
            Log::warning('postWelcomeCard failed', $response->json() ?: []);
        }
    }

    /**
     * Delete a team by ID.
     */
    public function deleteTeam(string $teamId): void
    {
        $response = $this->graph()->delete("/groups/{$teamId}");

        if (!$response->successful() && $response->status() !== 404) {
            Log::error('Graph deleteTeam error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Failed to delete team: ' . $response->body());
        }
    }

    /**
     * Get a team by ID.
     */
    public function getTeam(string $teamId): array
    {
        $response = $this->graph()->get("/teams/{$teamId}");

        if (!$response->successful()) {
            throw new \Exception('Failed to get team: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Poll until the team is fully provisioned (Graph API is async, returns 202).
     * Retries up to $maxAttempts times with a 3s delay between each.
     */
    public function waitForTeam(string $teamId, int $maxAttempts = 10): string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep(3);
            try {
                $team = $this->getTeam($teamId);
                if (!empty($team['id'])) return $team['id'];
            } catch (\Exception) {
                // Not ready yet, keep polling
            }
        }
        return $teamId; // Return as-is after timeout
    }

    /**
     * Create a private channel inside a team.
     * In application context, we must add the owner as a member after creation.
     */
    public function createPrivateChannel(
        string $teamId,
        string $channelName,
        string $ownerUpn
    ): array {
        // Get owner's object ID — retry a few times in case of transient failures
        $ownerId = null;
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $ownerUser = $this->graph()->get("/users/{$ownerUpn}")->json();
            $ownerId   = $ownerUser['id'] ?? null;
            if ($ownerId) break;
            sleep(2);
        }

        if (!$ownerId) {
            // Fallback: try searching by UPN via filter
            $search = $this->graph()->get('/users', ['$filter' => "userPrincipalName eq '{$ownerUpn}'"])->json();
            $ownerId = $search['value'][0]['id'] ?? null;
        }

        if (!$ownerId) {
            Log::error("createPrivateChannel: Could not resolve owner ID for UPN [{$ownerUpn}]");
            throw new \Exception("Could not find owner user: {$ownerUpn}");
        }

        $response = $this->graph()->post("/teams/{$teamId}/channels", [
            'displayName'    => $channelName,
            'membershipType' => 'private',
            'members'        => [
                [
                    '@odata.type'     => '#microsoft.graph.aadUserConversationMember',
                    'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('{$ownerId}')",
                    'roles'           => ['owner'],
                ],
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Graph createPrivateChannel error', $response->json() ?: []);
            throw new \Exception('Failed to create channel: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Add a user to a Team as an OWNER (for teachers).
     */
    public function addTeamOwner(string $teamId, string $upnOrId): void
    {
        // Resolve to object ID if UPN given
        $userId = $this->resolveUserId($upnOrId);

        $response = $this->graph()->post("/teams/{$teamId}/members", [
            '@odata.type'     => '#microsoft.graph.aadUserConversationMember',
            'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('{$userId}')",
            'roles'           => ['owner'],
        ]);

        if (!$response->successful() && $response->status() !== 409) {
            throw new \Exception('Failed to add team owner: ' . $response->body());
        }
    }

    /**
     * Add a user to a private channel as OWNER (for teachers).
     */
    public function addChannelOwner(string $teamId, string $channelId, string $upnOrId): void
    {
        $userId = $this->resolveUserId($upnOrId);

        $response = $this->graph()->post(
            "/teams/{$teamId}/channels/{$channelId}/members",
            [
                '@odata.type'     => '#microsoft.graph.aadUserConversationMember',
                'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('{$userId}')",
                'roles'           => ['owner'],
            ]
        );

        if (!$response->successful() && $response->status() !== 409) {
            throw new \Exception('Failed to add channel owner: ' . $response->body());
        }
    }

    /**
     * Resolve a UPN (email) or object ID to an Azure AD object ID.
     */
    public function resolveUserId(string $upnOrId): string
    {
        // If it looks like a GUID already, return as-is
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $upnOrId)) {
            return $upnOrId;
        }
        $response = $this->graph()->get("/users/" . urlencode($upnOrId) . '?$select=id');
        if (!$response->successful()) {
            throw new \Exception("Could not resolve user [{$upnOrId}]: " . $response->body());
        }
        return $response->json('id');
    }
}
