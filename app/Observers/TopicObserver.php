<?php

namespace App\Observers;

use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;
use App\Models\Topic;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        $topic->content = clean($topic->content, 'user_topic_content');
        $topic->excerpt = make_excerpt($topic->content);
    }

    public function saved(Topic $topic)
    {
//        if(!$topic->slug)
//        {
//            dispatch( new TranslateSlug($topic) );
//        }
    }
}
