<?php

namespace Tests\Feature;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdministratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
    }

    private function createAdministrator(
        array $attributes = []
    ): Administrator {
        return Administrator::create(array_merge([
            'Email' => 'administrator@example.com',
            'Name' => 'Test Administrator',
            'Role' => 'manager',
            'Phone' => '0500000000',
            'Password' => Hash::make('password123'),
            'Image' => '/upload/test.jpg',
        ], $attributes));
    }

    public function test_owner_can_list_administrators(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $this->createAdministrator([
            'Email' => 'manager@example.com',
        ]);

        $this->actingAs($owner)
            ->getJson('/api/administrator')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonMissingPath('0.Password');
    }

    public function test_owner_can_get_administrator_by_id(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $manager = $this->createAdministrator([
            'Email' => 'manager@example.com',
        ]);

        $this->actingAs($owner)
            ->getJson(
                "/api/administrator/{$manager->Administrator_ID}"
            )
            ->assertOk()
            ->assertJson([
                'Administrator_ID' => $manager->Administrator_ID,
                'Email' => 'manager@example.com',
                'Name' => 'Test Administrator',
                'Role' => 'manager',
            ])
            ->assertJsonMissingPath('Password');
    }

    public function test_getting_missing_administrator_returns_not_found(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $this->actingAs($owner)
            ->getJson('/api/administrator/999')
            ->assertNotFound()
            ->assertJson([
                'error' => 'Administrator not found',
            ]);
    }

    public function test_owner_can_create_administrator(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $response = $this->actingAs($owner)
            ->post('/api/administrator', [
                'Email' => 'sales@example.com',
                'Name' => 'Sales Administrator',
                'Role' => 'sales',
                'Phone' => '0501111111',
                'Password' => 'secret123',
                'Image' => UploadedFile::fake()->image('sales.jpg'),
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('Email', 'sales@example.com')
            ->assertJsonPath('Role', 'sales')
            ->assertJsonMissingPath('Password');

        $administrator = Administrator::where(
            'Email',
            'sales@example.com'
        )->firstOrFail();

        $this->assertTrue(
            Hash::check('secret123', $administrator->Password)
        );

        $this->assertTrue(
            Storage::disk('uploads')->exists(
                basename($administrator->Image)
            )
        );
    }

    public function test_administrator_email_must_be_unique(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $this->createAdministrator([
            'Email' => 'manager@example.com',
        ]);

        $this->actingAs($owner)
            ->withHeader('Accept', 'application/json')
            ->post('/api/administrator', [
                'Email' => 'manager@example.com',
                'Name' => 'Another Manager',
                'Role' => 'manager',
                'Phone' => '0502222222',
                'Password' => 'secret123',
                'Image' => UploadedFile::fake()->image('manager.jpg'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('Email');
    }

    public function test_owner_role_cannot_be_assigned_when_creating_administrator(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $this->actingAs($owner)
            ->withHeader('Accept', 'application/json')
            ->post('/api/administrator', [
                'Email' => 'another-owner@example.com',
                'Name' => 'Another Owner',
                'Role' => 'owner',
                'Phone' => '0502222222',
                'Password' => 'secret123',
                'Image' => UploadedFile::fake()->image('owner.jpg'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('Role');
    }

    public function test_owner_can_update_administrator_without_changing_password(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $manager = $this->createAdministrator([
            'Email' => 'manager@example.com',
        ]);

        $oldPassword = $manager->Password;

        $this->actingAs($owner)
            ->putJson(
                "/api/administrator/{$manager->Administrator_ID}",
                [
                    'Email' => 'updated@example.com',
                    'Name' => 'Updated Manager',
                    'Phone' => '0503333333',
                    'Role' => 'manager',
                ]
            )
            ->assertOk()
            ->assertJsonPath('Email', 'updated@example.com');

        $manager->refresh();

        $this->assertSame($oldPassword, $manager->Password);
    }

    public function test_owner_can_update_administrator_password(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $manager = $this->createAdministrator([
            'Email' => 'manager@example.com',
        ]);

        $this->actingAs($owner)
            ->putJson(
                "/api/administrator/{$manager->Administrator_ID}",
                [
                    'Email' => $manager->Email,
                    'Name' => $manager->Name,
                    'Phone' => $manager->Phone,
                    'Role' => $manager->Role,
                    'Password' => 'new-password',
                ]
            )
            ->assertOk();

        $manager->refresh();

        $this->assertTrue(
            Hash::check('new-password', $manager->Password)
        );
    }

    public function test_updating_image_removes_old_image(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        Storage::disk('uploads')->put('old.jpg', 'old image');

        $manager = $this->createAdministrator([
            'Email' => 'manager@example.com',
            'Image' => '/upload/old.jpg',
        ]);

        $response = $this->actingAs($owner)
            ->post(
                "/api/administrator/{$manager->Administrator_ID}",
                [
                    '_method' => 'PUT',
                    'Email' => $manager->Email,
                    'Name' => $manager->Name,
                    'Phone' => $manager->Phone,
                    'Role' => $manager->Role,
                    'Image' => UploadedFile::fake()->image('new.jpg'),
                ]
            );

        $response->assertOk();

        $manager->refresh();

        $this->assertTrue(
            Storage::disk('uploads')->exists(
                basename($manager->Image)
            )
        );

        $this->assertFalse(
            Storage::disk('uploads')->exists('old.jpg')
        );
    }

    public function test_manager_cannot_modify_owner(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $manager = $this->createAdministrator([
            'Email' => 'manager@example.com',
        ]);

        $this->actingAs($manager)
            ->putJson(
                "/api/administrator/{$owner->Administrator_ID}",
                [
                    'Email' => $owner->Email,
                    'Name' => 'Modified Owner',
                    'Phone' => $owner->Phone,
                    'Role' => 'owner',
                ]
            )
            ->assertForbidden()
            ->assertJson([
                'error' => 'Only an owner can modify an owner',
            ]);
    }

    public function test_owner_cannot_be_deleted(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        $this->actingAs($owner)
            ->deleteJson(
                "/api/administrator/{$owner->Administrator_ID}"
            )
            ->assertForbidden()
            ->assertJson([
                'error' => 'Owner cannot be removed',
            ]);

        $this->assertDatabaseHas('administrators', [
            'Administrator_ID' => $owner->Administrator_ID,
        ]);
    }

    public function test_deleting_administrator_removes_image(): void
    {
        $owner = $this->createAdministrator([
            'Email' => 'owner@example.com',
            'Role' => 'owner',
        ]);

        Storage::disk('uploads')->put(
            'manager.jpg',
            'manager image'
        );

        $manager = $this->createAdministrator([
            'Email' => 'manager@example.com',
            'Image' => '/upload/manager.jpg',
        ]);

        $this->actingAs($owner)
            ->deleteJson(
                "/api/administrator/{$manager->Administrator_ID}"
            )
            ->assertNoContent();

        $this->assertDatabaseMissing('administrators', [
            'Administrator_ID' => $manager->Administrator_ID,
        ]);

        $this->assertFalse(
            Storage::disk('uploads')->exists('manager.jpg')
        );
    }
}
