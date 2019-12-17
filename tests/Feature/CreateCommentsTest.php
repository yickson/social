<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\Status;
use App\Models\Comment;
use App\Events\CommentCreated;
use Illuminate\Support\Facades\Event;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_users_cannot_create_comments()
    {
        $status = factory(Status::class)->create();
        $comment = ['body' => 'Mi primer comentario'];

        $response = $this->postJson(route('statuses.comments.store', $status), $comment);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_comment_statuses()
    {
        $status = factory(Status::class)->create();
        $user = factory(User::class)->create();
        $comment = ['body' => 'Mi primer comentario'];

        $response = $this->actingAs($user)
            ->postJson(route('statuses.comments.store', $status), $comment);

        $response->assertJson([
            'data' => ['body' => $comment['body']]
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'status_id' => $status->id,
            'body' => $comment['body']
        ]);
    }

    /** @test */
    public function an_event_is_fired_when_a_comment_is_created()
    {
        Event::fake([CommentCreated::class]);
        Broadcast::shouldReceive('socket')->andReturn('socket-id');

        $status = factory(Status::class)->create();
        $user = factory(User::class)->create();
        $comment = ['body' => 'Mi primer comentario'];

        $this->actingAs($user)
            ->postJson(route('statuses.comments.store', $status), $comment);

        Event::assertDispatched(CommentCreated::class, function ($commentStatusEvent) {
            $this->assertInstanceOf(CommentResource::class, $commentStatusEvent->comment);
            $this->assertTrue(Comment::first()->is($commentStatusEvent->comment->resource));
            $this->assertEventChannelType('public', $commentStatusEvent);
            $this->assertEventChannelName("statuses.{$commentStatusEvent->comment->status_id}.comments", $commentStatusEvent);
            $this->assertDontBroadcastToCurrentUser($commentStatusEvent);

            return true;
        });
    }

    /** @test */
    public function a_comment_requires_a_body()
    {
        $status = factory(Status::class)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson(route('statuses.comments.store', $status), ['body' => '']);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'message', 'errors' => ['body']
        ]);
    }
}
