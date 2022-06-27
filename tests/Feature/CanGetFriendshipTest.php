<?php

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CanGetFriendshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_get_friendships()
    {
        $this->getJson(route('friendships.show', 'sebastian'))
            ->assertStatus(401);
    }

    /** @test */
    public function can_get_friendship()
    {
        $this->withoutExceptionHandling();

        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $friendship = Friendship::query()->create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id
        ]);

        $response = $this->actingAs($sender)->getJson(route('friendships.show', $recipient));

        $response->assertJsonFragment([
            'friendship_status' => $friendship->fresh()->status
        ]);

    }
}
