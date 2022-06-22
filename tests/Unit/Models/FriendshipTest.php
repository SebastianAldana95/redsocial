<?php

namespace Tests\Unit\Models;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_friendship_request_belongs_to_a_sender()
    {
        $sender = User::factory()->create();
        $friendship = Friendship::factory()->create(['sender_id' => $sender->id]);

        $this->assertInstanceOf(User::class, $friendship->sender);
    }

    /** @test */
    public function a_friendship_request_belongs_to_a_recipient()
    {
        $recipient = User::factory()->create();
        $friendship = Friendship::factory()->create(['recipient_id' => $recipient->id]);

        $this->assertInstanceOf(User::class, $friendship->recipient);
    }

    /** @test */
    public function can_find_friendships_by_sender_and_recipient() // podemos encontrar solicitudes de amistad por el usuario que recibe y envia
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Friendship::factory()->count(2)->create(['recipient_id' => $recipient->id]);
        Friendship::factory()->count(2)->create(['sender_id' => $sender->id]);

        Friendship::query()->create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id
        ]);

        $foundFriendship = Friendship::query()->betweenUsers($sender, $recipient)->first();

        $this->assertEquals($sender->id, $foundFriendship->sender_id);
        $this->assertEquals($recipient->id, $foundFriendship->recipient_id);

        $foundFriendship2 = Friendship::query()->betweenUsers($recipient, $sender)->first();

        $this->assertEquals($sender->id, $foundFriendship2->sender_id);
        $this->assertEquals($recipient->id, $foundFriendship2->recipient_id);

    }
}
