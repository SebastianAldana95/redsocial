<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\User;
use App\Traits\HasLikes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_comment_belongs_to_a_user()
    {
        $comment = Comment::factory()->create();

        $this->assertInstanceOf(User::class, $comment->user);
    }

    /** @test */
    public function a_comment_model_must_use_the_trait_has_likes() // El modelo comment debe utilizar el trait HasLikes
    {
        $this->assertClassUsesTrait(HasLikes::class, Comment::class);
    }

}
