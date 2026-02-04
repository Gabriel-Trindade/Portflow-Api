<?php

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Enums\ContainerType;
use App\Modules\Containers\Infrastructure\Models\Container;
use App\Modules\Users\Infrastructure\Models\User;

describe('GET /api/containers', function () {
    it('lists containers for authenticated users', function () {
        $user = User::factory()->create();

        Container::create([
            'code' => 'ABCD1234567',
            'type' => ContainerType::TwentyFeet,
            'status' => ContainerStatus::AwaitingUnloading,
        ]);

        $response = $this->actingAs($user)->getJson('/api/containers');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'code', 'type', 'status']],
                'meta',
                'links',
            ]);
    });

    it('filters containers by type', function () {
        $user = User::factory()->create();

        Container::create([
            'code' => 'ABCD1234567',
            'type' => ContainerType::TwentyFeet,
            'status' => ContainerStatus::AwaitingUnloading,
        ]);

        Container::create([
            'code' => 'WXYZ7654321',
            'type' => ContainerType::Reefer,
            'status' => ContainerStatus::AwaitingUnloading,
        ]);

        $response = $this->actingAs($user)->getJson('/api/containers?type=reefer');

        $response->assertOk();
        expect($response->json('meta.total'))->toBe(1);
    });

    it('fails for unauthenticated requests', function () {
        $this->getJson('/api/containers')->assertStatus(401);
    });
});

describe('POST /api/containers', function () {
    it('creates a container and normalizes code', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/containers', [
            'code' => 'abcd1234567',
            'type' => '40ft',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.code', 'ABCD1234567')
            ->assertJsonPath('data.type', '40ft')
            ->assertJsonPath('data.status', 'awaiting_unloading');

        $this->assertDatabaseHas('containers', ['code' => 'ABCD1234567']);
    });

    it('fails with invalid data', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/containers', [
            'code' => 'bad-code',
            'type' => 'unknown',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'type']);
    });

    it('fails for unauthenticated requests', function () {
        $this->postJson('/api/containers', [
            'code' => 'ABCD1234567',
            'type' => '20ft',
        ])->assertStatus(401);
    });
});

describe('GET /api/containers/{id}', function () {
    it('shows a container', function () {
        $user = User::factory()->create();
        $container = Container::create([
            'code' => 'ABCD1234567',
            'type' => ContainerType::TwentyFeet,
            'status' => ContainerStatus::AwaitingUnloading,
        ]);

        $response = $this->actingAs($user)->getJson("/api/containers/{$container->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $container->id);
    });

    it('returns 404 for non-existent container', function () {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/containers/9999')->assertStatus(404);
    });
});

describe('PUT /api/containers/{id}', function () {
    it('updates a container', function () {
        $user = User::factory()->create();
        $container = Container::create([
            'code' => 'ABCD1234567',
            'type' => ContainerType::TwentyFeet,
            'status' => ContainerStatus::AwaitingUnloading,
        ]);

        $response = $this->actingAs($user)->putJson("/api/containers/{$container->id}", [
            'code' => 'wxyz7654321',
            'type' => 'reefer',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.code', 'WXYZ7654321')
            ->assertJsonPath('data.type', 'reefer');

        $this->assertDatabaseHas('containers', ['code' => 'WXYZ7654321']);
    });
});

describe('PATCH /api/containers/{id}/status', function () {
    it('changes container status with valid transition', function () {
        $user = User::factory()->create();
        $container = Container::create([
            'code' => 'ABCD1234567',
            'type' => ContainerType::TwentyFeet,
            'status' => ContainerStatus::AwaitingUnloading,
        ]);

        $response = $this->actingAs($user)->patchJson("/api/containers/{$container->id}/status", [
            'status' => 'unloaded',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'unloaded');
    });

    it('rejects invalid status transition', function () {
        $user = User::factory()->create();
        $container = Container::create([
            'code' => 'ABCD1234567',
            'type' => ContainerType::TwentyFeet,
            'status' => ContainerStatus::AwaitingUnloading,
        ]);

        $response = $this->actingAs($user)->patchJson("/api/containers/{$container->id}/status", [
            'status' => 'released',
        ]);

        $response->assertStatus(422);
    });
});
