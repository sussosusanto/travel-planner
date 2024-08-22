<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreCreatesNewTravelAndClearsCache()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $travelData = [
            'origin' => 'Origin City',
            'destination' => 'Destination City',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'A nice trip.',
            'type' => 'single day',
        ];

        $response = $this->postJson('/api/travels', $travelData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Travel created successfully.',
                'data' => $travelData
            ]);

        $this->assertDatabaseHas('travels', [
            'origin' => 'Origin City',
            'destination' => 'Destination City',
            'type' => 'single day',
            'user_id' => $user->id,
        ]);
    }

    public function testShowReturnsSpecificTravel()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $travel = Travel::create([
            'origin' => 'City A',
            'destination' => 'Destination B',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'A nice trip.',
            'type' => 'single day',
            'user_id' => $user->id,
        ]);

        $response = $this->getJson("/api/travels/{$travel->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $travel->id,
                    'origin' => $travel->origin,
                    'destination' => $travel->destination,
                    'start_date' => $travel->start_date,
                    'end_date' => $travel->end_date,
                    'description' => $travel->description,
                    'type' => $travel->type,
                ]
            ]);
    }

    public function testUpdateModifiesExistingTravel()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $travel = Travel::create([
            'origin' => 'City A',
            'destination' => 'Destination B',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'A nice trip.',
            'type' => 'single day',
            'user_id' => $user->id,
        ]);

        $updateData = [
            'origin' => 'Updated Origin',
            'destination' => 'Updated Destination',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'An updated trip.',
            'type' => 'single day',
        ];

        $response = $this->putJson("/api/travels/{$travel->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Travel updated successfully.',
                'data' => $updateData
            ]);

        $this->assertDatabaseHas('travels', [
            'id' => $travel->id,
            'origin' => 'Updated Origin',
            'destination' => 'Updated Destination',
            'type' => 'single day',
        ]);
    }

    public function testDeleteRemovesTravel()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $travel = Travel::create([
            'origin' => 'City A',
            'destination' => 'Destination B',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'A nice trip.',
            'type' => 'single day',
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/travels/{$travel->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Travel deleted successfully.'
            ]);

        $this->assertDatabaseMissing('travels', [
            'id' => $travel->id,
        ]);
    }
}