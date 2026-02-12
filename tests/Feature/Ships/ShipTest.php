<?php

use App\Modules\Ships\Domain\Enums\ShipStatus;
use App\Modules\Ships\Infrastructure\Models\Ship;
use App\Modules\Users\Infrastructure\Models\User;

describe('GET /api/ships', function () {
    it('lists ships for authenticated users', function () {
        $user = User::factory()->create();

        Ship::create([
            'name' => 'Ever Glory',
            'imo' => '1234567',
            'eta' => '2026-02-20 08:00:00',
            'etd' => '2026-02-21 08:00:00',
            'status' => ShipStatus::Scheduled,
            'has_pending_operations' => false,
        ]);

        $response = $this->actingAs($user)->getJson('/api/ships');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'imo',
                    'eta',
                    'etd',
                    'status',
                    'berth_code',
                    'has_pending_operations',
                ]],
                'meta',
                'links',
            ]);
    });

    it('fails for unauthenticated requests', function () {
        $this->getJson('/api/ships')->assertStatus(401);
    });
});

describe('POST /api/ships', function () {
    it('creates a ship with eta and etd', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/ships', [
            'name' => 'Pacific Bridge',
            'imo' => '7654321',
            'eta' => '2026-02-20T08:00:00Z',
            'etd' => '2026-02-21T08:00:00Z',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Pacific Bridge')
            ->assertJsonPath('data.imo', '7654321')
            ->assertJsonPath('data.status', 'scheduled');

        $this->assertDatabaseHas('ships', ['imo' => '7654321']);
    });

    it('fails with invalid data', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/ships', [
            'name' => '',
            'imo' => 'ABC1234',
            'eta' => '2026-02-22T08:00:00Z',
            'etd' => '2026-02-21T08:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'imo', 'etd']);
    });
});

describe('GET /api/ships/{id}', function () {
    it('shows a ship', function () {
        $user = User::factory()->create();
        $ship = Ship::create([
            'name' => 'Atlas Carrier',
            'imo' => '1234987',
            'eta' => '2026-02-20 08:00:00',
            'etd' => '2026-02-21 08:00:00',
            'status' => ShipStatus::Scheduled,
            'has_pending_operations' => false,
        ]);

        $response = $this->actingAs($user)->getJson("/api/ships/{$ship->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $ship->id);
    });

    it('returns 404 for non-existent ship', function () {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/ships/9999')->assertStatus(404);
    });
});

describe('PUT /api/ships/{id}', function () {
    it('updates ship data', function () {
        $user = User::factory()->create();
        $ship = Ship::create([
            'name' => 'Atlas Carrier',
            'imo' => '5555555',
            'eta' => '2026-02-20 08:00:00',
            'etd' => '2026-02-21 08:00:00',
            'status' => ShipStatus::Scheduled,
            'has_pending_operations' => false,
        ]);

        $response = $this->actingAs($user)->putJson("/api/ships/{$ship->id}", [
            'name' => 'Atlas Carrier II',
            'has_pending_operations' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Atlas Carrier II')
            ->assertJsonPath('data.has_pending_operations', true);
    });
});

describe('PATCH /api/ships/{id}/dock', function () {
    it('docks a ship when berth is available', function () {
        $user = User::factory()->create();
        $ship = Ship::create([
            'name' => 'Blue Wave',
            'imo' => '2345678',
            'eta' => '2026-02-20 08:00:00',
            'etd' => '2026-02-21 08:00:00',
            'status' => ShipStatus::Scheduled,
            'has_pending_operations' => false,
        ]);

        $response = $this->actingAs($user)->patchJson("/api/ships/{$ship->id}/dock", [
            'berth_code' => 'b-01',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'docked')
            ->assertJsonPath('data.berth_code', 'B-01');
    });

    it('rejects docking when berth is occupied', function () {
        $user = User::factory()->create();

        Ship::create([
            'name' => 'Occupied One',
            'imo' => '3456789',
            'eta' => '2026-02-20 08:00:00',
            'etd' => '2026-02-21 08:00:00',
            'status' => ShipStatus::Docked,
            'berth_code' => 'A-02',
            'has_pending_operations' => false,
        ]);

        $ship = Ship::create([
            'name' => 'Trying Two',
            'imo' => '9876543',
            'eta' => '2026-02-20 09:00:00',
            'etd' => '2026-02-21 09:00:00',
            'status' => ShipStatus::Scheduled,
            'has_pending_operations' => false,
        ]);

        $this->actingAs($user)
            ->patchJson("/api/ships/{$ship->id}/dock", ['berth_code' => 'A-02'])
            ->assertStatus(422);
    });
});

describe('PATCH /api/ships/{id}/finalize', function () {
    it('finalizes a docked ship without pending operations', function () {
        $user = User::factory()->create();
        $ship = Ship::create([
            'name' => 'Final Vessel',
            'imo' => '1112223',
            'eta' => '2026-02-20 08:00:00',
            'etd' => '2026-02-21 08:00:00',
            'status' => ShipStatus::Docked,
            'berth_code' => 'B-03',
            'has_pending_operations' => false,
        ]);

        $response = $this->actingAs($user)->patchJson("/api/ships/{$ship->id}/finalize");

        $response->assertOk()
            ->assertJsonPath('data.status', 'finalized');
    });

    it('rejects finalization when ship has pending operations', function () {
        $user = User::factory()->create();
        $ship = Ship::create([
            'name' => 'Blocked Vessel',
            'imo' => '1112224',
            'eta' => '2026-02-20 08:00:00',
            'etd' => '2026-02-21 08:00:00',
            'status' => ShipStatus::Docked,
            'berth_code' => 'B-03',
            'has_pending_operations' => true,
        ]);

        $this->actingAs($user)
            ->patchJson("/api/ships/{$ship->id}/finalize")
            ->assertStatus(422);
    });
});
