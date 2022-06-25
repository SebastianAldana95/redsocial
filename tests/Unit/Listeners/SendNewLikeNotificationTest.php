<?php

namespace Tests\Unit\Listeners;

use App\Events\ModelLiked;
use App\Models\Status;
use App\Models\User;
use App\Notifications\NewLikeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendNewLikeNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_notification_is_sent_when_a_user_receives_a_new_like()
    {
        Notification::fake();

        $statusOwner = User::factory()->create(); // usuarios que crea el estado
        $likeSender = User::factory()->create(); // usuario que da like al estado

        $status = Status::factory()->create(['user_id' => $statusOwner->id]); // estado

        $status->likes()->create([  // like
            'user_id' => $likeSender->id
        ]);

        ModelLiked::dispatch($status, $likeSender);

        Notification::assertSentTo(
            $statusOwner,
            NewLikeNotification::class,
            function ($notification, $channels) use ($status, $likeSender) {
                $this->assertContains('database', $channels);
                $this->assertContains('broadcast', $channels);
                $this->assertTrue($notification->model->is($status));
                $this->assertTrue($notification->likeSender->is($likeSender));
                $this->assertInstanceOf(BroadcastMessage::class, $notification->toBroadcast($status->user));
                return true; // return true para verificar que el test paso, de lo contrario para
        });
    }
}
