<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\DatabaseNotificationFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class CanManageNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_access_notifications()
    {
        $this->getJson(route('notifications.index'))->assertStatus(401);
    }


    /** @test */
    public function authenticated_users_can_get_their_notifications() //usuarios autenticados pueden ver sus notificaciones
    {
        $this->withExceptionHandling();
        $user = User::factory()->create();
        $notification = DatabaseNotification::query()->create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\ExampleNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => [
                'link' => url('/'),
                'message' => 'Mensaje de la notificación'
            ],
            'read_at' => null
        ]);

        $this->actingAs($user)->getJson(route('notifications.index'))
            ->assertJson([
                [
                    'data' => [
                        'link' => $notification->data['link'],
                        'message' => $notification->data['message']
                    ]]
            ])
        ;
    }

    /** @test */
    public function guests_users_cannot_mark_notifications()
    {
        $notification = DatabaseNotification::query()->create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\ExampleNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => 1,
            'data' => [
                'link' => url('/'),
                'message' => 'Mensaje de la notificación'
            ],
            'read_at' => null
        ]);

        $this->postJson(route('read-notifications.store', $notification))->assertStatus(401);
        $this->deleteJson(route('read-notifications.destroy', $notification))->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_mark_notifications_as_read()
    {
        $user = User::factory()->create();
        $notification = DatabaseNotification::query()->create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\ExampleNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => [
                'link' => url('/'),
                'message' => 'Mensaje de la notificación'
            ],
            'read_at' => null
        ]);

        $response = $this->actingAs($user)->postJson(route('read-notifications.store', $notification));

        $response->assertJson($notification->fresh()->toArray());

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function authenticated_users_can_mark_notifications_as_unread()
    {
        $user = User::factory()->create();
        $notification = DatabaseNotification::query()->create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\ExampleNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => [
                'link' => url('/'),
                'message' => 'Mensaje de la notificación'
            ],
            'read_at' => now()
        ]);

        $response = $this->actingAs($user)->deleteJson(route('read-notifications.destroy', $notification));

        $response->assertJson($notification->fresh()->toArray());

        $this->assertNull($notification->fresh()->read_at); // verifica que si es null
    }
}
