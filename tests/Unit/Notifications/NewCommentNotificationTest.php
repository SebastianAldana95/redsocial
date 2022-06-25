<?php

namespace Tests\Unit\Notifications;

use App\Models\Comment;
use App\Models\Status;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewCommentNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_notification_is_stored_in_the_database()
    {
        $status = Status::factory()->create();
        $comment = Comment::factory()->create(['status_id' => $status->id]);

        $statusOwner = $status->user;

        $statusOwner->notify(new NewCommentNotification($comment));

        $this->assertCount(1, $statusOwner->notifications);

        $notificationsData = $statusOwner->notifications->first()->data;

        $this->assertEquals($comment->path(), $notificationsData['link']);

        $this->assertEquals("{$comment->user->name} comentó tu publicación", $notificationsData['message']);

    }
}
