<?php

namespace Tests\Integration\Repository;

use App\Models\User;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(EloquentUserRepository::class);
    }

    /** @test */
    public function test_that_entity_method_exists_in_user_repository()
    {
        $this->assertEquals(true, method_exists($this->repository, 'entity'));
    }

    /** test */
    public function test_return_user_class_for_entity_function()
    {
        $this->assertEquals(User::class, $this->repository->entity());
    }

    /** @test */
    public function test_return_all_for_user_repository()
    {
        $users = User::factory(10)->create();

        $this->assertEquals($users->count(), $this->repository->all()->count());
    }

    /** @test */
    public function test_find_where_in_user_repository()
    {
        $user = User::factory()->create([
            'email' => 'test@test.test'
        ]);

        $this->assertEquals(
            $user->id,
            $this->repository->findWhere('email', '=', 'test@test.test')
                ->first()
                ->id
        );
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
