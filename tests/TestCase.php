<?php

namespace Tests;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function assertClassUsesTrait($trait, $class)
    {
        $this->assertArrayHasKey(
            $trait,
            class_uses($class),
            "{$class} must use {$trait} trait"
        );
    }

    protected function assertDontBroadcastToCurrentUser($event, $socketId = 'socket-id')
    {
        $this->assertInstanceOf(ShouldBroadcast::class, $event);

        $this->assertEquals(
            $socketId, // Generate by Broadcast::shouldReceive('socket')->andReturn('socket-id');
            $event->socket,
            'The event ' . get_class($event) . ' must call the method "dontBroadcastToCurrentUser" in the constructor.'
        );
    }

    protected function assertEventChannelType($channelType, $event)
    {
        $type = [
            'public' => Channel::class,
            'private' => PrivateChannel::class,
            'presence' => PresenceChannel::class,
        ];

        $this->assertEquals($type[$channelType], get_class($event->broadcastOn()));
    }

    protected function assertEventChannelName($channelName, $event)
    {
        $this->assertEquals($channelName, $event->broadcastOn()->name);
    }
}
