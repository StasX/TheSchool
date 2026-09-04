<?php
namespace Tests\Feature;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdministrator(array $attributes = []): Administrator
    {
        return Administrator::create(array_merge([
            'Email' => 'owner@example.com',
            'Name' => 'Test Owner',
            'Role' => 'owner',
            'Phone' => '0500000000',
            'Password' => Hash::make('password123'),
            'Image' => '/upload/test.jpg',
        ], $attributes));
    }

    public function test_administrator_can_login_with_valid_credentials(): void
    {
        $administrator = $this->createAdministrator();

        $response = $this->postJson('/api/login', [
            'Email' => 'owner@example.com',
            'Password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath(
                'administrator.Administrator_ID',
                $administrator->Administrator_ID
            )
            ->assertJsonPath(
                'administrator.Email',
                $administrator->Email
            )
            ->assertJsonPath(
                'administrator.Role',
                'owner'
            )
            ->assertJsonStructure([
                'administrator' => [
                    'Administrator_ID',
                    'Email',
                    'Name',
                    'Role',
                    'Image',
                ],
                'token',
            ]);

        $this->assertAuthenticatedAs($administrator);
    }

    public function test_login_fails_with_incorrect_password(): void
    {
        $this->createAdministrator();

        $this->postJson('/api/login', [
            'Email' => 'owner@example.com',
            'Password' => 'incorrect-password',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);

        $this->assertGuest();
    }

    public function test_login_fails_with_unknown_email(): void
    {
        $this->postJson('/api/login', [
            'Email' => 'unknown@example.com',
            'Password' => 'password123',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);

        $this->assertGuest();
    }

    public function test_login_fails_when_credentials_are_missing(): void
    {
        $this->postJson('/api/login', [])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);

        $this->assertGuest();
    }

    public function test_authenticated_administrator_can_get_own_data(): void
    {
        $administrator = $this->createAdministrator();

        $this->actingAs($administrator)
            ->getJson('/api/auth')
            ->assertOk()
            ->assertJson([
                'Administrator_ID' => $administrator->Administrator_ID,
                'Email' => $administrator->Email,
                'Name' => $administrator->Name,
                'Role' => $administrator->Role,
                'Phone' => $administrator->Phone,
                'Image' => $administrator->Image,
            ])
            ->assertJsonMissing([
                'Password' => $administrator->Password,
            ]);
    }

    public function test_guest_cannot_get_authenticated_user_data(): void
    {
        $this->getJson('/api/auth')
            ->assertUnauthorized();
    }

    public function test_authenticated_administrator_can_logout(): void
    {
        $administrator = $this->createAdministrator();

        $this->actingAs($administrator)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJson([
                'message' => 'Logout successful',
            ]);

        $this->assertGuest();
    }

    public function test_guest_cannot_access_protected_endpoints(): void
    {
        $this->getJson('/api/student')
            ->assertUnauthorized();

        $this->getJson('/api/course')
            ->assertUnauthorized();

        $this->getJson('/api/administrator')
            ->assertUnauthorized();
    }

    public function test_login_rejects_missing_email(): void
    {
        $this->postJson('/api/login', [
            'Password' => 'password123',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);
        $this->assertGuest();
    }

    public function test_login_rejects_invalid_email(): void
    {
        $this->postJson('/api/login', [
            'Email' => 'not-an-email',
            'Password' => 'password123',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);
        $this->assertGuest();
    }

    public function test_login_rejects_missing_password(): void
    {
        $this->postJson('/api/login', [
            'Email' => 'owner@example.com',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);
        $this->assertGuest();
    }

    public function test_login_regenerates_session(): void
    {
        $this->createAdministrator();

        $oldSessionId = session()->getId();

        $this->postJson('/api/login', [
            'Email' => 'owner@example.com',
            'Password' => 'password123',
        ])->assertOk();

        $this->assertNotSame($oldSessionId, session()->getId());
    }

    public function test_login_rejects_non_string_email(): void
    {
        $this->postJson('/api/login', [
            'Email' => ['owner@example.com'],
            'Password' => 'password123',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);
        $this->assertGuest();
    }

    public function test_login_rejects_empty_password(): void
    {
        $this->postJson('/api/login', [
            'Email' => 'owner@example.com',
            'Password' => '',
        ])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);
        $this->assertGuest();
    }

    public function test_login_rejects_non_string_password(): void
    {
        $this->postJson('/api/login', [
            'Email' => 'owner@example.com',
            'Password' => ['password123'],
        ])
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid username or password.',
            ]);
        $this->assertGuest();
    }
}
