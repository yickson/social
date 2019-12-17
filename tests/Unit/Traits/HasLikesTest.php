<?php

namespace Tests\Unit\Traits;

use App\User;
use Tests\TestCase;
use App\Models\Like;
use App\Traits\HasLikes;
use App\Events\ModelLiked;
use App\Events\ModelUnliked;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasLikesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Schema::create('model_with_likes', function ($table) {
            $table->increments('id');
        });

        Event::fake([ModelLiked::class, ModelUnliked::class]);
    }

    /** @test */
    public function a_model_morph_many_likes()
    {
        $model = new ModelWithLike(['id' => 1]);

        factory(Like::class)->create([
            'likeable_id' => $model->id,
            'likeable_type' => get_class($model),
        ]);

        $this->assertInstanceOf(Like::class, $model->likes->first());
    }

    /** @test */
    public function a_model_can_be_liked_and_unlike()
    {
        $model = ModelWithLike::create();

        $this->actingAs(factory(User::class)->create());

        $model->like();

        $this->assertEquals(1, $model->likes()->count());

        $model->unlike();

        $this->assertEquals(0, $model->likes()->count());
    }

    /** @test */
    public function a_model_can_be_liked_once()
    {
        $model = ModelWithLike::create();

        $this->actingAs(factory(User::class)->create());

        $model->like();

        $this->assertEquals(1, $model->likes()->count());

        $model->like();

        $this->assertEquals(1, $model->likes()->count());
    }

    /** @test */
    public function a_model_knows_if_it_has_been_liked()
    {
        $model = ModelWithLike::create();

        $this->assertFalse($model->isLiked());

        $this->actingAs(factory(User::class)->create());

        $this->assertFalse($model->isLiked());

        $model->like();

        $this->assertTrue($model->isLiked());
    }

    /** @test */
    public function a_model_knows_how_many_likes_it_has()
    {
        $model = new ModelWithLike(['id' => 1]);

        $this->assertEquals(0, $model->likesCount());

        factory(Like::class, 2)->create([
            'likeable_id' => $model->id,
            'likeable_type' => get_class($model)
        ]);

        $this->assertEquals(2, $model->likesCount());
    }

    /** @test */
    public function an_event_is_fired_when_a_model_is_liked()
    {
        Broadcast::shouldReceive('socket')->andReturn('socket-id');

        $this->actingAs($likeSender = factory(User::class)->create());

        $model = new ModelWithLike(['id' => 1]);

        $model->like();

        Event::assertDispatched(ModelLiked::class, function ($event) use ($likeSender) {
            $this->assertInstanceOf(ModelWithLike::class, $event->model);
            $this->assertTrue($event->likeSender->is($likeSender));
            $this->assertEventChannelType('public', $event);
            $this->assertEventChannelName($event->model->eventChannelName(), $event);
            $this->assertDontBroadcastToCurrentUser($event);

            return true;
        });
    }

    /** @test */
    public function an_event_is_fired_when_a_model_is_unliked()
    {
        Broadcast::shouldReceive('socket')->andReturn('socket-id');

        $this->actingAs(factory(User::class)->create());

        $model = ModelWithLike::create();

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

    /** @test */
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
    use HasLikes;

    public $timestamps = false;

    protected $fillable = ['id'];

    public function path()
    {
        // TODO: Implement path() method.
    }
}
