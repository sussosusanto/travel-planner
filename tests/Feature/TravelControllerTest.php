<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Mockery;
use App\Enums\StatusCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Contracts\TravelRepositoryInterface;

class TravelControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $travelRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure a clean state for each test
        if (\DB::transactionLevel() > 0) {
            \DB::rollBack();
        }

        \DB::beginTransaction();

        // Mock the TravelRepositoryInterface
        $this->travelRepository = Mockery::mock(TravelRepositoryInterface::class);
        $this->app->instance(TravelRepositoryInterface::class, $this->travelRepository);
    }

    public function testIndexReturnsDataFromCache()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cacheKey = "user_{$user->id}_travels_page_1_per_page_10";

        Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);
        Cache::shouldReceive('get')->once()->with($cacheKey)->andReturn(['some_data']);

        Log::shouldReceive('info')->once()->with('Cache hit');

        $response = $this->getJson('/api/travels');

        $response->assertStatus(StatusCode::OK)
            ->assertJson([
                'data' => ['some_data']
            ]);
    }

    public function testIndexFetchesDataFromDatabaseIfNotCached()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cacheKey = "user_{$user->id}_travels_page_1_per_page_10";

        Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(false);

        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, 60, Mockery::on(function ($callback) use ($user) {
                // Simulate the paginate call inside the callback
                $this->travelRepository->shouldReceive('paginate')->once()->with(10)->andReturn(['db_data']);
                $result = $callback(); // Call the callback
                return $result === ['db_data'];
            }))
            ->andReturn(['db_data']);

        Log::shouldReceive('info')->once()->with('Database hit');

        $response = $this->getJson('/api/travels');

        $response->assertStatus(StatusCode::OK)
            ->assertJson([
                'data' => ['db_data']
            ]);
    }

    public function testStoreValidatesRequestAndClearsCache()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelData = [
            'origin' => 'Origin City',
            'destination' => 'Destination City',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'A nice trip.',
            'type' => 'single day',
        ];

        Cache::shouldReceive('flush')->once();

        $this->travelRepository->shouldReceive('create')
            ->once()
            ->with(array_merge($travelData, ['user_id' => $user->id]))
            ->andReturn($travelData);

        $response = $this->postJson('/api/travels', $travelData);

        $response->assertStatus(StatusCode::OK)
            ->assertJson([
                'message' => 'Travel created successfully.',
                'data' => $travelData
            ]);
    }

    public function testShowReturnsTravelData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelId = 1;
        $travelData = ['id' => $travelId, 'origin' => 'Origin City'];

        $this->travelRepository->shouldReceive('find')
            ->once()
            ->with($travelId)
            ->andReturn($travelData);

        $response = $this->getJson("/api/travels/{$travelId}");

        $response->assertStatus(StatusCode::OK)
            ->assertJson([
                'data' => $travelData
            ]);
    }

    public function testUpdateValidatesRequestAndUpdatesTravel()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelId = 1;
        $travelData = [
            'origin' => 'Updated Origin',
            'destination' => 'Updated Destination',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'An updated trip.',
            'type' => 'single day',
        ];

        $this->travelRepository->shouldReceive('update')
            ->once()
            ->with($travelId, $travelData)
            ->andReturn(array_merge(['id' => $travelId], $travelData));

        $response = $this->putJson("/api/travels/{$travelId}", $travelData);

        $response->assertStatus(StatusCode::OK)
            ->assertJson([
                'message' => 'Travel updated successfully.',
                'data' => array_merge(['id' => $travelId], $travelData)
            ]);
    }

    public function testDestroyDeletesTravel()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelId = 1;

        $this->travelRepository->shouldReceive('delete')
            ->once()
            ->with($travelId)
            ->andReturn(true);

        $response = $this->deleteJson("/api/travels/{$travelId}");

        $response->assertStatus(StatusCode::OK)
            ->assertJson([
                'message' => 'Travel deleted successfully.'
            ]);
    }

    protected function tearDown(): void
    {
        \DB::rollBack();
        Mockery::close();
        parent::tearDown();
    }
}
