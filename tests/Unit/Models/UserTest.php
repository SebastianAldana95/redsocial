<?php

namespace Models;

use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function route;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test  */
    public function route_key_name_is_set_to_name()
    {
        $user = User::factory()->make();

        $this->assertEquals('name', $user->getRouteKeyName());
    }

    /** @test  */
    public function user_has_a_link_to_their_profile()
    {
        $user = User::factory()->make();

        $this->assertEquals(route('users.show', $user), $user->link());
    }

    /** @test  */
    public function user_has_an_avatar()
    {
        $user = User::factory()->make();

        $this->assertEquals('/img/avatar.jpg', $user->avatar());
        $this->assertEquals('/img/avatar.jpg', $user->avatar);
    }

    /** @test  */
    public function a_users_has_many_statuses()
    {
        $user = User::factory()->create();

        Status::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Status::class, $user->statuses()->first());
    }
}
