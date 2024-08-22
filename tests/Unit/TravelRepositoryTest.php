<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Travel;
use App\Models\User;
use App\Repositories\Eloquent\TravelRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class TravelRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $travelRepository;
    protected $travelModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Travel model
        $this->travelModel = Mockery::mock(Travel::class);
        $this->travelRepository = new TravelRepository($this->travelModel);
    }

    public function testPaginateReturnsUserTravels()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $perPage = 10;
        $expectedTravels = new \Illuminate\Pagination\LengthAwarePaginator(
            [
                new Travel(['user_id' => $user->id, 'origin' => 'City A', 'destination' => 'City B']),
                new Travel(['user_id' => $user->id, 'origin' => 'City C', 'destination' => 'City D']),
            ],
            2, // Total items
            $perPage,
            1 // Current page
        );

        $this->travelModel->shouldReceive('where')
            ->with('user_id', $user->id)
            ->andReturnSelf();

        $this->travelModel->shouldReceive('paginate')
            ->with($perPage)
            ->andReturn($expectedTravels);

        $travels = $this->travelRepository->paginate($perPage);

        $this->assertEquals($expectedTravels, $travels);
    }

    public function testFindReturnsTravelById()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelId = 1;
        $expectedTravel = new Travel(['id' => $travelId, 'user_id' => $user->id, 'origin' => 'City A']);

        $this->travelModel->shouldReceive('where')
            ->with('user_id', $user->id)
            ->andReturnSelf();

        $this->travelModel->shouldReceive('find')
            ->with($travelId)
            ->andReturn($expectedTravel);

        $travel = $this->travelRepository->find($travelId);

        $this->assertEquals($expectedTravel, $travel);
    }

    public function testCreateStoresTravelData()
    {
        $travelData = [
            'origin' => 'Origin City',
            'destination' => 'Destination City',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'description' => 'A nice trip.',
            'user_id' => 1
        ];

        $this->travelModel->shouldReceive('create')
            ->with($travelData)
            ->andReturn((object) $travelData);

        $travel = $this->travelRepository->create($travelData);

        $this->assertEquals((object) $travelData, $travel);
    }

    public function testUpdateReturnsFalseIfTravelNotFound()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelId = 1;
        $updateData = ['origin' => 'Updated Origin'];

        $this->travelModel->shouldReceive('where')
            ->with('user_id', $user->id)
            ->andReturnSelf();

        $this->travelModel->shouldReceive('find')
            ->with($travelId)
            ->andReturn(null);

        $result = $this->travelRepository->update($travelId, $updateData);

        $this->assertFalse($result);
    }

    public function testUpdateUpdatesTravelData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelId = 1;
        $updateData = ['origin' => 'Updated Origin'];

        // Mock the Travel instance
        $existingTravel = Mockery::mock(Travel::class);
        
        $this->travelModel->shouldReceive('where')
            ->with('user_id', $user->id)
            ->andReturnSelf();

        $this->travelModel->shouldReceive('find')
            ->with($travelId)
            ->andReturn($existingTravel);

        // Set expectation on the update method of the mocked Travel instance
        $existingTravel->shouldReceive('update')
            ->with($updateData)
            ->andReturn(true);

        $result = $this->travelRepository->update($travelId, $updateData);

        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseIfTravelNotFound()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelId = 1;

        $this->travelModel->shouldReceive('where')
            ->with('user_id', $user->id)
            ->andReturnSelf();

        $this->travelModel->shouldReceive('find')
            ->with($travelId)
            ->andReturn(null);

        $result = $this->travelRepository->delete($travelId);

        $this->assertFalse($result);
    }

    public function testDeleteRemovesTravel()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $travelId = 1;

        // Mock the Travel instance
        $existingTravel = Mockery::mock(Travel::class);
        
        $this->travelModel->shouldReceive('where')
            ->with('user_id', $user->id)
            ->andReturnSelf();

        $this->travelModel->shouldReceive('find')
            ->with($travelId)
            ->andReturn($existingTravel);

        // Set expectation on the delete method of the mocked Travel instance
        $existingTravel->shouldReceive('delete')
            ->andReturn(true);

        $result = $this->travelRepository->delete($travelId);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
