<?php

namespace Tests\Unit\Traits;

use App\Events\ModelLiked;
use App\Events\ModelUnliked;
use App\Models\Like;
use App\Models\User;
use App\Traits\HasLikes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HasLikesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        if (Schema::hasTable('model_with_likes')) return;

        Schema::create('model_with_likes', function (Blueprint $table) {
            $table->increments('id');
        });

        // Event::fake([ModelLiked::class, ModelUnliked::class]);

    }

    /** @test */
    public function a_model_morph_many_likes() //clase polimorfica para los likes, un modelo puede tener muchos likes
    {
        $model = ModelWithLike::query()->create();

        Like::factory()->create([
            'likeable_id' => $model->id,
            'likeable_type' => get_class($model)
        ]);

        $this->assertInstanceOf(Like::class, $model->likes->first());
    }

    /**
     * @test
     */
    public function a_model_can_be_liked_and_unlike() // un estado puede ser gustado o deslikeado
    {
        Event::fake([ModelLiked::class, ModelUnliked::class]);

        $model = ModelWithLike::query()->create();

        $this->actingAs(User::factory()->create());

        $model->like();

        $this->assertEquals(1, $model->likes()->count());

        $model->unlike();

        $this->assertEquals(0, $model->likes()->count());
    }

    /**
     * @test
     */
    public function a_model_can_be_liked_once() // un estado puede ser gustado una sola vez
    {
        Event::fake([ModelLiked::class]);

        $model = ModelWithLike::query()->create();

        $this->actingAs(User::factory()->create());

        $model->like();

        $this->assertEquals(1, $model->likes()->count());

        $model->like();

        $this->assertEquals(1, $model->likes()->count());
    }

    /**
     * @test
     */
    public function a_model_know_if_it_been_liked() // un estado sabe que ha sido gustado
    {
        Event::fake([ModelLiked::class]);

        $model = ModelWithLike::query()->create();

        $this->assertFalse($model->isLiked());

        $this->actingAs(User::factory()->create());

        $this->assertFalse($model->isLiked());

        $model->like();

        $this->assertTrue($model->isLiked());
    }

    /**
     * @test
     */
    public function a_model_knows_how_many_likes_it_has() // un estado sabe cuantos likes tiene
    {
        $model = ModelWithLike::query()->create();

        $this->assertEquals(0, $model->likesCount());

        Like::factory()->count(2)->create([
            'likeable_id' => $model->id,
            'likeable_type' => get_class($model)
        ]);

        $this->assertEquals(2, $model->likesCount());
    }

    /**
     * @test
     */
    public function an_event_is_fired_when_a_model_is_liked()
    {
        Event::fake([ModelLiked::class]);
        Broadcast::shouldReceive('socket')->andReturn('socket-id');

        $this->actingAs($likeSender = User::factory()->create());

        $model = new ModelWithLike(['id' => 1]);

        $model->like();

        Event::assertDispatched(ModelLiked::class, function ($event) use ($likeSender){
            $this->assertInstanceOf(ModelWithLike::class, $event->model);
            $this->assertTrue($event->likeSender->is($likeSender));
            $this->assertEventChannelType('public', $event);
            $this->assertEventChannelName($event->model->eventChannelName(), $event);
            $this->assertDontBroadcastToCurrentUser($event);

            return true;
        });
    }

    /**
     * @test
     */
    public function an_event_is_fired_when_a_model_is_unliked()
    {
        Event::fake([ModelUnliked::class]);
        Broadcast::shouldReceive('socket')->andReturn('socket-id');

        $this->actingAs(User::factory()->create());

        $model = ModelWithLike::query()->create();

        $model->likes()->firstOrCreate([
            'user_id' => auth()->id()
        ]);

        $model->unlike();

        Event::assertDispatched(ModelUnliked::class, function ($event) {
            $this->assertInstanceOf(ModelWithLike::class, $event->model);
            $this->assertEventChannelType('public', $event);
            $this->assertEventChannelName($event->model->eventChannelName(), $event);
            $this->assertDontBroadcastToCurrentUser($event);

            return true;
        });
    }

    /** @test  */
    public function can_get_the_event_channel_name()
    {
        $model = new ModelWithLike(['id' => 1]);

        $this->assertEquals(
            "modelwithlikes.1.likes",
            $model->eventChannelName()
        );
    }
}

class ModelWithLike extends Model
{
    use HasLikes, Notifiable;

    public $timestamps = false;
    protected $fillable = ['id'];

    public function path()
    {
        // TODO: Implement path() method.
    }
}
