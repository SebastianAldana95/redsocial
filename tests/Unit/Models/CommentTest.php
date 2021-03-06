<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\Status;
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
    public function a_comment_belongs_to_a_status()
    {
        $comment = Comment::factory()->create();

        $this->assertInstanceOf(Status::class, $comment->status);
    }

    /** @test */
    public function a_comment_model_must_use_the_trait_has_likes() // El modelo comment debe utilizar el trait HasLikes
    {
        $this->assertClassUsesTrait(HasLikes::class, Comment::class);
    }

    /** @test */
    public function a_comment_must_have_a_path()
    {
        $comment = Comment::factory()->create();
        // lo envia al estado que contiene el comentario -> lo resalta y hace scroll hasta la ubicacion del comentario
        // statuses/1#coment-1   para ubicar el comentario en la pagina
        $this->assertEquals(route('statuses.show', $comment->status_id) . '#comment-' . $comment->id, $comment->path());
    }

}
