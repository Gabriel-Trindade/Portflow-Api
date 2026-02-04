<?php

use App\Modules\Users\Domain\Enums\UserRole;
use App\Modules\Users\Domain\Enums\UserStatus;
use App\Modules\Users\Infrastructure\Models\User;

describe('GET /api/users/me', function () {
    it('returns the authenticated user profile', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/users/me');

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    });

    it('fails for unauthenticated request', function () {
        $this->getJson('/api/users/me')->assertStatus(401);
    });
});

describe('PUT /api/users/me', function () {
    it('updates the authenticated user profile', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/users/me', [
            'name' => 'Updated Name',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name');
    });

    it('rejects duplicate email on update', function () {
        $existing = User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/users/me', [
            'email' => 'taken@example.com',
        ]);

        $response->assertStatus(409);
    });
});

describe('PUT /api/users/me/password', function () {
    it('changes password with correct current password', function () {
        $user = User::factory()->create(['password' => 'old-password']);

        $response = $this->actingAs($user)->putJson('/api/users/me/password', [
            'current_password' => 'old-password',
            'new_password' => 'new-password123',
            'new_password_confirmation' => 'new-password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Password changed successfully.');
    });

    it('fails with wrong current password', function () {
        $user = User::factory()->create(['password' => 'old-password']);

        $response = $this->actingAs($user)->putJson('/api/users/me/password', [
            'current_password' => 'wrong-password',
            'new_password' => 'new-password123',
            'new_password_confirmation' => 'new-password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    });
});

describe('GET /api/users (admin)', function () {
    it('lists users for admin', function () {
        $admin = User::factory()->admin()->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($admin)->getJson('/api/users');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'email', 'role', 'status']],
                'meta',
                'links',
            ]);

        expect($response->json('meta.total'))->toBe(6); // admin + 5 users
    });

    it('filters users by role', function () {
        $admin = User::factory()->admin()->create();
        User::factory()->viewer()->count(3)->create();
        User::factory()->count(2)->create(); // operators

        $response = $this->actingAs($admin)->getJson('/api/users?role=viewer');

        $response->assertOk();
        expect($response->json('meta.total'))->toBe(3);
    });

    it('searches users by name or email', function () {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $response = $this->actingAs($admin)->getJson('/api/users?search=John');

        $response->assertOk();
        expect($response->json('meta.total'))->toBe(1);
    });

    it('denies access to non-admin users', function () {
        $operator = User::factory()->create(); // default role: operator

        $this->actingAs($operator)->getJson('/api/users')->assertStatus(403);
    });
});

describe('GET /api/users/{id} (admin)', function () {
    it('shows a specific user', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->getJson("/api/users/{$user->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id);
    });

    it('returns 404 for non-existent user', function () {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->getJson('/api/users/9999')->assertStatus(404);
    });
});

describe('PUT /api/users/{id}/role (admin)', function () {
    it('assigns a role to a user', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}/role", [
            'role' => 'viewer',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.role', 'viewer');

        expect($user->fresh()->role)->toBe(UserRole::Viewer);
    });

    it('prevents admin from changing own role', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->putJson("/api/users/{$admin->id}/role", [
            'role' => 'operator',
        ]);

        $response->assertStatus(422);
    });

    it('rejects invalid role value', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}/role", [
            'role' => 'superadmin',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    });
});

describe('DELETE /api/users/{id} (admin)', function () {
    it('deactivates a user', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/users/{$user->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'User deactivated successfully.');

        expect($user->fresh()->status)->toBe(UserStatus::Inactive);
    });

    it('prevents admin from deactivating themselves', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(422);
    });

    it('returns 404 for non-existent user', function () {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->deleteJson('/api/users/9999')->assertStatus(404);
    });
});
