<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_teacher_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('teacher.login'));
    }

    public function test_guest_cannot_access_teacher_dashboard(): void
    {
        $response = $this->get(route('teacher.dashboard'));

        $response->assertRedirect(route('teacher.login'));
    }

    public function test_teacher_can_login_with_credentials(): void
    {
        $teacher = User::create([
            'name' => 'AMIS Teacher',
            'email' => 'teacher@amis.edu.ph',
            'username' => 'teacher',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
            'account_status' => 'verified',
        ]);

        $response = $this->post(route('teacher.login.store'), [
            'teacher_id' => 'teacher@amis.edu.ph',
            'password' => 'teacher123',
        ]);

        $response->assertRedirect(route('teacher.dashboard'));
        $this->assertTrue(session()->get('teacher_portal_authenticated'));
        $this->assertEquals('teacher@amis.edu.ph', session()->get('teacher_email'));
    }

    public function test_authenticated_teacher_can_view_dashboard(): void
    {
        User::create([
            'name' => 'AMIS Teacher',
            'email' => 'teacher@amis.edu.ph',
            'username' => 'teacher',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
            'account_status' => 'verified',
        ]);

        $response = $this->withSession([
            'teacher_portal_authenticated' => true,
            'teacher_name' => 'AMIS Teacher',
            'teacher_email' => 'teacher@amis.edu.ph',
        ])->get(route('teacher.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Your Teaching Dashboard');
        $response->assertSee('Subject Load');
    }

    public function test_teacher_can_create_class_and_channels_with_mocked_ms_teams(): void
    {
        config(['services.microsoft.admin_upn' => 'admin@amis.edu.ph']);

        // Mock MicrosoftGraphService
        $mockGraph = $this->mock(\App\Services\MicrosoftGraphService::class);
        $mockGraph->shouldReceive('createTeam')
            ->once()
            ->with('G5 - GOLD [Boys & F2F]')
            ->andReturn(['id' => 'mock-team-id', 'displayName' => 'G5 - GOLD [Boys & F2F]']);
        
        $mockGraph->shouldReceive('waitForTeam')
            ->once()
            ->with('mock-team-id')
            ->andReturn('mock-team-id');

        $mockGraph->shouldReceive('getGeneralChannelId')
            ->once()
            ->with('mock-team-id')
            ->andReturn('mock-general-channel-id');

        $mockGraph->shouldReceive('postWelcomeCard')
            ->once()
            ->with('mock-team-id', 'mock-general-channel-id', \Mockery::type('array'));

        $mockGraph->shouldReceive('addTeamOwner')
            ->once()
            ->with('mock-team-id', 'teacher@amis.edu.ph');

        $mockGraph->shouldReceive('createPrivateChannel')
            ->once()
            ->with('mock-team-id', 'Mathematics', 'admin@amis.edu.ph')
            ->andReturn(['id' => 'mock-math-channel-id']);

        $mockGraph->shouldReceive('postWelcomeCard')
            ->once()
            ->with('mock-team-id', 'mock-math-channel-id', \Mockery::type('array'));

        $mockGraph->shouldReceive('addChannelOwner')
            ->once()
            ->with('mock-team-id', 'mock-math-channel-id', 'teacher@amis.edu.ph');

        // Create teacher user
        User::create([
            'name' => 'AMIS Teacher',
            'email' => 'teacher@amis.edu.ph',
            'username' => 'teacher',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
            'account_status' => 'verified',
        ]);

        // Hit store subject endpoint
        $response = $this->withSession([
            'teacher_portal_authenticated' => true,
            'teacher_name' => 'AMIS Teacher',
            'teacher_email' => 'teacher@amis.edu.ph',
        ])->post(route('teacher.subjects.store'), [
            'grade' => 'Grade 5',
            'name' => 'GOLD',
            'gender' => 'male',
            'mode' => 'Face-to-Face',
            'channels' => ['Mathematics']
        ]);

        $response->assertRedirect(route('teacher.subjects'));
        $response->assertSessionHas('success');

        // Verify database records
        $this->assertDatabaseHas('sections', [
            'name' => 'GOLD',
            'grade_level' => 'Grade 5',
            'learning_mode' => 'Face-to-Face',
            'gender' => 'male',
            'ms_team_id' => 'mock-team-id',
        ]);

        $this->assertDatabaseHas('section_subjects', [
            'subject_name' => 'Mathematics',
            'teacher_name' => 'Ust. Raffy Lingasa',
            'ms_channel_id' => 'mock-math-channel-id',
        ]);
    }
}
