<?php

namespace Tests\Unit\Notifications;

use App\Notifications\NewLikeNotification;
use App\User;
use Tests\TestCase;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewLikeNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_notification_is_stored_in_the_database()
    {
        $statusOwner = factory(User::class)->create();
        $likeSender = factory(User::class)->create();

        $status = factory(Status::class)->create(['user_id' => $statusOwner->id]);

        $status->likes()->create(['user_id' => $likeSender->id]);

        $statusOwner->notify(new NewLikeNotification($status, $likeSender));

        $this->assertCount(1, $statusOwner->notifications);

        $notificationsData = $statusOwner->notifications->first()->data;

        $this->assertEquals($status->path(), $notificationsData['link']);

        $this->assertEquals("Al usuario {$likeSender->name} le gustó tu publicación.", $notificationsData['message']);
    }
}
