<?php

namespace Tests\Browser;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class UsersCanLikeCommentsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @throws Throwable
     */
    public function users_can_like_and_unlike_comment()
    {

        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $comment) {
            $browser->loginAs($user)
                ->visit('/')
                ->waitForText($comment->body) // ya se renderizaron los estados con VUE // ver el cuerpo del comentario
                ->assertSeeIn('@comment-likes-count', 0)
                ->press('@comment-like-btn')
                ->waitForText('TE GUSTA')
                ->assertSee('TE GUSTA')
                ->pause(3000)
                ->assertSeeIn('@comment-likes-count', 1)

                ->press('@comment-like-btn')
                ->waitForText('ME GUSTA')
                ->assertSee('ME GUSTA')
                ->pause(3000)
                ->assertSeeIn('@comment-likes-count', 0)
            ;
        });
    }

    /**
     * @test
     * @throws Throwable
     */
    public function users_can_see_likes_in_real_time()
    {

        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $this->browse(function (Browser $browser1, Browser $browser2) use ($user, $comment) {
            $browser1->visit('/');

            $browser2->loginAs($user)
                ->visit('/')
                ->waitForText($comment->body) // ya se renderizaron los estados con VUE // ver el cuerpo del comentario
                ->assertSeeIn('@comment-likes-count', 0)
                ->press('@comment-like-btn')
                ->waitForText('TE GUSTA')
            ;

            $browser1->assertSeeIn('@comment-likes-count', 1);

            $browser2->press('@comment-like-btn')
                ->waitForText('ME GUSTA')
            ;

            $browser1->pause(3000)->assertSeeIn('@comment-likes-count', 0);
        });
    }
}
