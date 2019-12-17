<?php

namespace Tests\Feature;

use App\Models\Friendship;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CanSeeFriendsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guests_cannot_access_the_list_of_friends()
    {
        $this->get(route('friends.index'))->assertRedirect('login');
    }
    
    /** @test */
    function a_user_can_see_a_list_of_friends()
    {
        $sender = factory(User::class)->create();
        $recipient = factory(User::class)->create();

        factory(Friendship::class)->create([
            'recipient_id' => $recipient->id,
            'sender_id' => $sender->id,
            'status' => 'accepted'
        ]);

        $this->actingAs($sender)->get(route('friends.index'))->assertSee($recipient->name);
        $this->actingAs($recipient)->get(route('friends.index'))->assertSee($sender->name);
    }
}
