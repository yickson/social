<?php

namespace Tests\Unit\Models;

use App\Models\Friendship;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FriendshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_friendship_request_belongs_to_a_sender()
    {
        $sender = factory(User::class)->create();

        $friendship = factory(Friendship::class)->create(['sender_id' => $sender->id]);

        $this->assertInstanceOf(User::class, $friendship->sender);
    }

    /** @test */
    public function a_friendship_request_belongs_to_a_recipient()
    {
        $recipient = factory(User::class)->create();

        $friendship = factory(Friendship::class)->create(['recipient_id' => $recipient->id]);

        $this->assertInstanceOf(User::class, $friendship->recipient);
    }

    /** @test */
    public function can_find_friendships_by_sender_and_recipient()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        factory(Friendship::class, 2)->create(['recipient_id' => $recipient->id]);
        factory(Friendship::class, 2)->create(['sender_id' => $sender->id]);

        Friendship::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
        ]);

        $foundFriendship = Friendship::betweenUsers($sender, $recipient)->first();

        $this->assertEquals($sender->id, $foundFriendship->sender_id);
        $this->assertEquals($recipient->id, $foundFriendship->recipient_id);

        $foundFriendship2 = Friendship::betweenUsers($recipient, $sender)->first();

        $this->assertEquals($sender->id, $foundFriendship2->sender_id);
        $this->assertEquals($recipient->id, $foundFriendship2->recipient_id);
    }
}
