<?php

namespace Tests\Browser;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class UserCanRequestFriendshipTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test
     * @throws Throwable
     */
    public function guests_cannot_create_friendship_requests()
    {
        $recipient = User::factory()->create();

        $this->browse(function (Browser $browser) use ($recipient) {
            $browser->visit(route('users.show', $recipient))
                ->press('@request-friendship')
                ->assertPathIs('/login')
            ;
        });
    }

    /** @test
     * @throws Throwable
     */
    public function senders_can_create_and_delete_friendship_requests()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $this->browse(function (Browser $browser) use ($sender, $recipient) {
            $browser->loginAs($sender)
                ->visit(route('users.show', $recipient))
                ->press('@request-friendship')
                ->waitForText('Cancelar solicitud')
                ->assertSee('Cancelar solicitud')
                ->visit(route('users.show', $recipient))
                ->waitForText('Cancelar solicitud')
                ->assertSee('Cancelar solicitud')
                ->press('@request-friendship')
                ->waitForText('Solicitar amistad')
                ->assertSee('Solicitar amistad')
            ;
        });
    }

    /** @test
     * @throws Throwable
     */
    public function a_user_cannot_send_friend_request_to_itself() // un usuario no puede enviarse una solicitud a si mismo
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('users.show', $user))
                ->assertMissing('@request-friendship')
                ->assertSee('Eres t??')
            ;
        });
    }

    /** @test
     * @throws Throwable
     */
    public function senders_can_delete_accepted_friendship_requests()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Friendship::query()->create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'accepted'
        ]);

        $this->browse(function (Browser $browser) use ($sender, $recipient) {
            $browser->loginAs($sender)
                ->visit(route('users.show', $recipient))
                ->waitForText('Eliminar de mis amigos')
                ->assertSee('Eliminar de mis amigos')
                ->press('@request-friendship')
                ->waitForText('Solicitar amistad')
                ->assertSee('Solicitar amistad')
                ->visit(route('users.show', $recipient))
                ->waitForText('Solicitar amistad')
                ->assertSee('Solicitar amistad')
            ;
        });
    }

    /** @test
     * @throws Throwable
     */
    public function senders_cannot_delete_denied_friendship_requests()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Friendship::query()->create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'denied'
        ]);

        $this->browse(function (Browser $browser) use ($sender, $recipient) {
            $browser->loginAs($sender)
                ->visit(route('users.show', $recipient))
                ->waitForText('Solicitud denegada')
                ->assertSee('Solicitud denegada')
                ->press('@request-friendship')
                ->waitForText('Solicitud denegada')
                ->assertSee('Solicitud denegada')
                ->visit(route('users.show', $recipient))
                ->waitForText('Solicitud denegada')
                ->assertSee('Solicitud denegada')
            ;
        });
    }

    /** @test
     * @throws Throwable
     */
    public function recipients_can_accept_friendship_requests()
    {
        // $this->markTestIncomplete();
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Friendship::query()->create([
           'sender_id' => $sender->id,
           'recipient_id' => $recipient->id
        ]);

        $this->browse(function (Browser $browser) use ($sender, $recipient) {
            $browser->loginAs($recipient)
                ->visit(route('accept-friendships.index'))
                ->assertSee($sender->name)
                ->press('@accept-friendship')
                ->pause(2000)
                ->waitForText('son amigos', 7)
                ->assertSee('son amigos')
                ->visit(route('accept-friendships.index'))
                ->assertSee('son amigos')
            ;
        });
    }

    /** @test
     * @throws Throwable
     */
    public function recipients_can_deny_friendship_requests()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Friendship::query()->create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id
        ]);

        $this->browse(function (Browser $browser) use ($sender, $recipient) {
            $browser->loginAs($recipient)
                ->visit(route('accept-friendships.index'))
                ->assertSee($sender->name)
                ->press('@deny-friendship')
                ->waitForText('Solicitud denegada')
                ->assertSee('Solicitud denegada')
                ->visit(route('accept-friendships.index'))
                ->assertSee('Solicitud denegada')
            ;
        });
    }

    /** @test
     * @throws Throwable
     */
    public function recipients_can_delete_friendship_requests()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Friendship::query()->create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id
        ]);

        $this->browse(function (Browser $browser) use ($sender, $recipient) {
            $browser->loginAs($recipient)
                ->visit(route('accept-friendships.index'))
                ->assertSee($sender->name)
                ->press('@delete-friendship')
                ->waitForText('Solicitud eliminada')
                ->assertSee('Solicitud eliminada')
                ->visit(route('accept-friendships.index'))
                ->assertDontSee('Solicitud eliminada')
                ->assertDontSee($sender->name)
            ;
        });
    }
}
