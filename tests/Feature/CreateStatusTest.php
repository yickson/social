<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\Status;
use App\Events\StatusCreated;
use Illuminate\Support\Facades\Event;
use App\Http\Resources\StatusResource;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_can_not_create_statuses()
    {
        $response = $this->postJson(route('statuses.store'), ['body' => 'Mi primer status']);

        $response->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_can_create_statuses()
    {
        Event::fake([StatusCreated::class]);

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson(route('statuses.store'), ['body' => 'Mi primer status']);

        $response->assertJson([
            'data' => ['body' => 'Mi primer status'],
        ]);

        $this->assertDatabaseHas('statuses', [
            'user_id' => $user->id,
            'body' => 'Mi primer status'
        ]);
    }

    /** @test */
    public function an_event_is_fired_when_a_status_is_created()
    {
        Event::fake([StatusCreated::class]);
        Broadcast::shouldReceive('socket')->andReturn('socket-id');

        $user = factory(User::class)->create();

        $this->actingAs($user)->postJson(route('statuses.store'), ['body' => 'Mi primer status']);

        Event::assertDispatched(StatusCreated::class, function ($statusCreatedEvent) {
            $this->assertInstanceOf(StatusResource::class, $statusCreatedEvent->status);
            $this->assertTrue(Status::first()->is($statusCreatedEvent->status->resource));
            $this->assertEventChannelType('public', $statusCreatedEvent);
            $this->assertEventChannelName('statuses', $statusCreatedEvent);
            $this->assertDontBroadcastToCurrentUser($statusCreatedEvent);

            return true;
        });
    }

    /** @test */
    public function a_status_requires_a_body()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson(route('statuses.store'), ['body' => '']);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'message', 'errors' => ['body']
        ]);
    }

    /** @test */
    public function a_status_body_requires_a_minimum_length()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson(route('statuses.store'), ['body' => 'asdf']);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'message', 'errors' => ['body']
        ]);
    }
}
