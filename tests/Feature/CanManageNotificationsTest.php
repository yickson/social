<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanManageNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_access_notifications()
    {
        $this->getJson(route('notifications.index'))->assertStatus(401);
    }
    
    /** @test */
    public function authenticated_users_can_get_their_notifications()
    {
        $user = factory(User::class)->create();

        $notification = factory(DatabaseNotification::class)->create(['notifiable_id' => $user->id]);

        $this->actingAs($user)->getJson(route('notifications.index'))
            ->assertJson([
                [
                    'data' => [
                        'link' => $notification->data['link'],
                        'message' => $notification->data['message'],
                    ]
                ]
            ]);
    }

    /** @test */
    public function guests_users_cannot_mark_notifications()
    {
        $notification = factory(DatabaseNotification::class)->create();

        $this->postJson(route('read-notifications.store', $notification))->assertStatus(401);
        $this->deleteJson(route('read-notifications.destroy', $notification))->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_mark_notifications_as_read()
    {
        $user = factory(User::class)->create();

        $notification = factory(DatabaseNotification::class)->create([
            'notifiable_id' => $user->id,
            'read_at' => null
        ]);

        $response = $this->actingAs($user)->postJson(route('read-notifications.store', $notification));

        $response->assertJson($notification->fresh()->toArray());

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function authenticated_users_can_mark_notifications_as_unread()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $notification = factory(DatabaseNotification::class)->create([
            'notifiable_id' => $user->id,
            'read_at' => now()
        ]);

        $response = $this->actingAs($user)->deleteJson(route('read-notifications.destroy', $notification));

        $response->assertJson($notification->fresh()->toArray());

        $this->assertNull($notification->fresh()->read_at);
    }
}
