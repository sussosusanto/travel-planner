<?php

namespace App\Http\Controllers;

use Exception;
use App\Enums\StatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\TravelRepositoryInterface;

class TravelController extends Controller
{
    protected $travelRepository;

    public function __construct(TravelRepositoryInterface $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    public function index()
    {

        $userId = auth()->id();
        $page = request()->get('page', 1);
        $perPage = 10;
        $cacheKey = "user_{$userId}_travels_page_{$page}_per_page_{$perPage}";

        if (Cache::has($cacheKey)) {
            $travels = Cache::get($cacheKey);
            Log::info('Cache hit');

        } else {
            Log::info('Database hit');
            $travels = Cache::remember($cacheKey, 60, function () use ($perPage) {
                return $this->travelRepository->paginate($perPage);
            });
        }

        return response()->json([
            'data' => $travels,
        ], StatusCode::OK);
    }

    public function store(Request $request)
    {
        try {
            $userId = auth()->id();

            $validatedData = $request->validate([
                'origin' => 'required|string',
                'destination' => 'required|string',
                'type' => 'required|string',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'required|string',
            ]);

            $validatedData['user_id'] = auth()->id();

            $travel = $this->travelRepository->create($validatedData);

            // Clear cache, there is better way to do this but for now this will do
            Cache::flush();

            return response()->json([
                'message' => 'Travel created successfully.',
                'data' => $travel
            ], StatusCode::OK);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], StatusCode::VALIDATION_ERROR);

        } catch (Exception $e) {
            Log::error('Error creating travel: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while creating the travel.',
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    public function show($id)
    {
        try {
            $travel = $this->travelRepository->find($id);

            if (!$travel) {
                return response()->json([
                    'message' => 'Travel record not found.',
                ], StatusCode::NOT_FOUND);
            }
            return response()->json([
                'data' => $travel,
            ], StatusCode::OK);

        } catch (Exception $e) {
            Log::error('Error fetching travel record: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching the travel record.',
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'origin' => 'required|string',
                'destination' => 'required|string',
                'type' => 'required|string',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'required|string',
            ]);


            $travel = $this->travelRepository->update($id, $validatedData);

            if (!$travel) {
                return response()->json([
                    'message' => 'Travel record not found.',
                ], StatusCode::NOT_FOUND);
            }

            return response()->json([
                'message' => 'Travel updated successfully.',
                'data' => $travel
            ], StatusCode::OK);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], StatusCode::VALIDATION_ERROR);

        } catch (Exception $e) {
            Log::error('Error updating travel: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating the travel.',
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {


            $travel = $this->travelRepository->delete($id);

            if (!$travel) {
                return response()->json([
                    'message' => 'Travel record not found.',
                ], StatusCode::NOT_FOUND);
            }

            return response()->json([
                'message' => 'Travel deleted successfully.',
            ], StatusCode::OK);

        } catch (Exception $e) {
            Log::error('Error deleting travel: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting the travel.',
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
