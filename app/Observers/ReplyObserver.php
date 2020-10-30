<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        $reply->body = clean($reply->body, 'user_topic_body');
    }

    public function created(Reply $reply)
    {
        $reply->topic->updateReplyCount();

        if(! app()->runningInConsole())
        {
            $reply->topic->user->topicNotify(new TopicReplied($reply));
        }
    }

    public function deleted(Reply $reply)
    {
        $reply->topic->updateReplyCount();
    }
}
