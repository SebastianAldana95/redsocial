<?php

namespace Tests\Unit\Notifications;

use App\Models\Status;
use App\Models\User;
use App\Notifications\NewLikeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewLikeNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_notification_is_stored_in_the_database()
    {
        $statusOwner = User::factory()->create();
        $likeSender = User::factory()->create();

        $status = Status::factory()->create(['user_id' => $statusOwner->id]);

        $status->likes()->create(['user_id' => $likeSender->id]);

        $statusOwner->notify(new NewLikeNotification($status, $likeSender)); // envia la notificacion

        $this->assertCount(1, $statusOwner->notifications);

        $notificationsData = $statusOwner->notifications->first()->data;

        $this->assertEquals($status->path(), $notificationsData['link']);
        $this->assertEquals("Al usuario {$likeSender->name} le gustó tu publicación", $notificationsData['message']);

        // [['link' => 'model', 'message' => 'Al usuario Sebastian le gustó tu model']]

    }
}
