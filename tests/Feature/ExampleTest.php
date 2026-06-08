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
        $response->assertSee('Handled Subjects');
    }
}
