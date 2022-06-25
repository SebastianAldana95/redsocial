<?php

namespace Tests\Browser;

use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class UsersCanGetTheirNotificationsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @throws Throwable
     */
    public function users_can_see_their_notifications_in_the_navbar()
    {
        $user = User::factory()->create();

        $status = Status::factory()->create();

        $notification = DatabaseNotification::query()->create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\ExampleNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Has recibido un like',
                'link' => route('statuses.show', $status)
            ],
            'read_at' => null
        ]);

        $this->browse(function (Browser $browser) use ($user, $notification, $status) {
            $browser->loginAs($user)
                ->visit('/')
                //->resize(1024, 768)
                ->click('@notifications')
                ->pause(1000)
                ->assertSee('Has recibido un like')
                ->click("@{$notification->id}")
                ->assertUrlIs($status->path())

                ->click('@notifications')
                ->pause(1000)
                ->press("@mark-as-read-{$notification->id}")
                ->waitFor("@mark-as-unread-{$notification->id}")
                ->assertMissing("@mark-as-read-{$notification->id}")

                ->press("@mark-as-unread-{$notification->id}")
                ->waitFor("@mark-as-read-{$notification->id}")
                ->assertMissing("@mark-as-unread-{$notification->id}")
            ;
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function users_can_see_their_like_notifications_in_real_time()
    {
        $user1 = User::factory()->create(); //recibe like
        $user2 = User::factory()->create(); //da like al estado del usuario 1

        $status = Status::factory()->create(['user_id' => $user1->id]);

        $this->browse(function (Browser $browser1, Browser $browser2) use ($user1, $user2, $status) {
            $browser1->loginAs($user1)
                ->visit('/')
            ;

            $browser2->loginAs($user2)
                ->visit('/')
                ->waitForText($status->body)
                ->press('@like-btn')
                ->pause(3000)
            ;

            $browser1->assertSeeIn('@notifications-count', 1);
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function users_can_see_their_comment_notifications_in_real_time()
    {
        $user1 = User::factory()->create(); //recibe like
        $user2 = User::factory()->create(); //da like al estado del usuario 1

        $status = Status::factory()->create(['user_id' => $user1->id]);

        $this->browse(function (Browser $browser1, Browser $browser2) use ($user1, $user2, $status) {
            $browser1->loginAs($user1)
                ->visit('/')
                ->resize(1024, 768)
            ;

            $browser2->loginAs($user2)
                ->visit('/')
                ->waitForText($status->body)
                ->type('comment', 'Mi comentario')
                ->press('@comment-btn')
                ->pause(3000)
            ;

            $browser1->assertSeeIn('@notifications-count', 1);
        });
    }
}
