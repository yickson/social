<?php

namespace Tests\Unit\Listeners;

use Notification;
use Tests\TestCase;
use App\Models\Status;
use App\Models\Comment;
use App\Events\CommentCreated;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SendNewCommentNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_notification_is_sent_when_a_status_receives_a_new_comment()
    {
        Notification::fake();

        $status = factory(Status::class)->create();
        $comment = factory(Comment::class)->create(['status_id' => $status->id]);

        CommentCreated::dispatch($comment);

        Notification::assertSentTo(
            $status->user,
            NewCommentNotification::class,
            function ($notification, $channels) use ($comment, $status) {
                $this->assertContains('database', $channels);
                $this->assertContains('broadcast', $channels);
                $this->assertTrue($notification->comment->is($comment));
                $this->assertInstanceOf(BroadcastMessage::class, $notification->toBroadcast($status->user));
                return true;
            }
        );
    }
}
