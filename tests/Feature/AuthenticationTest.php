<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_private_pages(): void
    {
        foreach (['/dashboard', '/analytics', '/settings', '/components/cards', '/examples/records'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_login_page_posts_credentials_to_fortify(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('action="'.route('login.store').'"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="remember"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_log_in_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $ignored) {
            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertTooManyRequests();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect();

        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect();
    }

    public function test_admin_layout_shows_the_authenticated_user_and_logout_form(): void
    {
        $user = User::factory()->create(['name' => 'Sari Wulandari', 'email' => 'sari@example.test']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Sari Wulandari')
            ->assertSee('sari@example.test')
            ->assertSee('SW')
            ->assertSee('action="'.route('logout').'"', false)
            ->assertDontSee('Ayu Rahmawati');
    }

    public function test_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Intruder',
            'email' => 'intruder@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(404);

        $this->assertDatabaseMissing('users', ['email' => 'intruder@example.test']);
    }

    public function test_forgot_password_page_renders(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('action="'.route('password.email').'"', false)
            ->assertSee('name="email"', false);
    }

    public function test_reset_link_can_be_requested(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_page_and_submission_work(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('action="'.route('password.update').'"', false)
            ->assertSee('name="token"', false)
            ->assertSee('value="'.$token.'"', false);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-secret-password', $user->fresh()->password));
    }

    public function test_settings_page_updates_profile_and_password(): void
    {
        $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.test']);

        $this->actingAs($user)
            ->put(route('user-profile-information.update'), ['name' => 'New Name', 'email' => 'new@example.test'])
            ->assertSessionHasNoErrors();

        $this->assertSame('New Name', $user->fresh()->name);

        $this->actingAs($user)
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'another-secret-pass',
                'password_confirmation' => 'another-secret-pass',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('another-secret-pass', $user->fresh()->password));
    }

    public function test_settings_page_is_wired_to_fortify_forms(): void
    {
        $user = User::factory()->create(['name' => 'Sari Wulandari', 'email' => 'sari@example.test']);

        $this->actingAs($user)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('action="'.route('user-profile-information.update').'"', false)
            ->assertSee('action="'.route('user-password.update').'"', false)
            ->assertSee('value="sari@example.test"', false)
            ->assertDontSee('Ayu Rahmawati');
    }

    public function test_password_update_rejects_wrong_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('admin.settings'))
            ->put(route('user-password.update'), [
                'current_password' => 'not-the-password',
                'password' => 'another-secret-pass',
                'password_confirmation' => 'another-secret-pass',
            ])
            ->assertRedirect(route('admin.settings'))
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
