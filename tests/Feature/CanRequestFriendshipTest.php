<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\Friendship;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanRequestFriendshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_create_friendship_request()
    {
        $recipient = factory(User::class)->create();

        $response = $this->postJson(route('friendships.store', $recipient));

        $response->assertStatus(401);
    }

    /** @test */
    public function can_create_friendship_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $response = $this->actingAs($sender)->postJson(route('friendships.store', $recipient));

        $response->assertJson([
           'friendship_status' => 'pending'
        ]);

        $this->assertDatabaseHas('friendships', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending'
        ]);

        $this->actingAs($sender)->postJson(route('friendships.store', $recipient));
        $this->assertCount(1, Friendship::all());
    }

    /** @test */
    public function a_user_cannot_send_friend_request_to_itself()
    {
        $sender = factory(User::class)->create();

        $this->actingAs($sender)->postJson(route('friendships.store', $sender));

        $this->assertDatabaseMissing('friendships', [
            'sender_id' => $sender->id,
            'recipient_id' => $sender->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function guests_users_cannot_delete_friendship_request()
    {
        $recipient = factory(User::class)->create();

        $response = $this->deleteJson(route('friendships.destroy', $recipient));

        $response->assertStatus(401);
    }

    /** @test */
    public function senders_can_delete_sent_friendship_request()
    {
        $this->withoutExceptionHandling();

        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        Friendship::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
        ]);

        $response = $this->actingAs($sender)->deleteJson(route('friendships.destroy', $recipient));

        $response->assertJson([
            'friendship_status' => 'deleted'
        ]);

        $this->assertDatabaseMissing('friendships', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
        ]);
    }

    /** @test */
    public function senders_cannot_delete_denied_friendship_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        Friendship::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'denied'
        ]);

        $response = $this->actingAs($sender)->deleteJson(route('friendships.destroy', $recipient));

        $response->assertJson([
            'friendship_status' => 'denied'
        ]);

        $this->assertDatabaseHas('friendships', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'denied'
        ]);
    }

    /** @test */
    public function recipients_can_delete_denied_friendship_request()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        Friendship::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'denied'
        ]);

        $response = $this->actingAs($recipient)->deleteJson(route('friendships.destroy', $sender));

        $response->assertJson([
            'friendship_status' => 'deleted'
        ]);

        $this->assertDatabaseMissing('friendships', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'denied'
        ]);
    }

    /** @test */
    public function recipients_can_delete_received_friendship_request()
    {
        $this->withoutExceptionHandling();

        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        Friendship::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
        ]);

        $response = $this->actingAs($recipient)->deleteJson(route('friendships.destroy', $sender));

        $response->assertJson([
            'friendship_status' => 'deleted'
        ]);

        $this->assertDatabaseMissing('friendships', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
        ]);
    }

    /** @test */
    public function guests_users_cannot_accept_friendship_request()
    {
        $user = factory(User::class)->create();

        $this->postJson(route('accept-friendships.store', $user))
            ->assertStatus(401);

        $this->get(route('accept-friendships.index'))
            ->assertRedirect('login');
    }

    /** @test */
    public function can_accept_friendship_request()
    {
        $this->withoutExceptionHandling();

        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        Friendship::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($recipient)->postJson(route('accept-friendships.store', $sender));

        $response->assertJson([
            'friendship_status' => 'accepted'
        ]);

        $this->assertDatabaseHas('friendships', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'accepted'
        ]);
    }

    /** @test */
    public function can_get_all_friendship_requests_received()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        $sender->sendFriendRequestTo($recipient);
        factory(Friendship::class, 2)->create();

        $this->actingAs($recipient);

        $response = $this->get(route('accept-friendships.index'));

        $this->assertCount(1, $response->viewData('friendshipRequests'));
    }

    /** @test */
    public function guests_users_cannot_deny_friendship_request()
    {
        $user = factory(User::class)->create();

        $response = $this->deleteJson(route('accept-friendships.destroy', $user));

        $response->assertStatus(401);
    }

    /** @test */
    public function can_deny_friendship_request()
    {
        $this->withoutExceptionHandling();

        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        Friendship::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($recipient)->deleteJson(route('accept-friendships.destroy', $sender));

        $response->assertJson([
            'friendship_status' => 'denied'
        ]);

        $this->assertDatabaseHas('friendships', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'denied'
        ]);
    }
}
