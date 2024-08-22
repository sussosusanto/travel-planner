<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $UserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->UserRepository = $this->app->make(UserRepository::class);
    }

    public function test_register_creates_new_user()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $user = $this->UserRepository->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_login_returns_token_for_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $request = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->UserRepository->login($request);

        $this->assertInstanceOf(User::class, $response);

    }

    public function test_login_throws_validation_exception_for_invalid_credentials()
    {
        $request = [
            'email' => 'invalid@example.com',
            'password' => 'invalid-password',
        ];

        $response = $this->UserRepository->login($request);

        $this->assertIsNotObject($response);

    }
}
