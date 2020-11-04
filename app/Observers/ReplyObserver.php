<?php

namespace App\Observers;

use App\Models\Reply;
use App\Models\User;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        if(! app()->runningInConsole()){
//         净化用户输入的评论
            $body = clean($reply->body, 'user_topic_body');
//        保存用户
            $users=[];
//        找出所有@TA
            preg_match_all("/@.*?(?=( |$))/", $body,$matched_name);
            foreach ($matched_name[0] as $key => &$value)
            {
//        循环处理，如果评论只写了@，净化会在末尾加上HTML标签，处理掉HTML标签
                if($stop = strrpos($value, '</')){
                    $value = substr($value, 0, $stop);
                }
//          如果没找到这个用户，或者@的人与作者相同
                $id = User::where('name', 'like binary', substr($value, 1))->value('id');
                if( ! $id || $id === $reply->topic->user->id)
                {
//              释放这个@
                    $matched_name[0][$key] = null;
                } else {
                    $users[$key]['id']  =   $id;
                    $users[$key]['name'] = $value;
                    $users[$key]['link'] = '<a href="'. asset('users/'. $id) .'">'. $value .'</a>';
                }
            }
            foreach($users as $user)
            {
                if(!strpos($body,$user['link'])){
                    $body = str_replace($user['name'], $user['link'], $body);
                }
            }
            $reply->body = $body;
            session(['at_users' => array_unique($users, SORT_REGULAR)]);
        }
    }

    public function created(Reply $reply)
    {
        $reply->topic->updateReplyCount();
        if(! app()->runningInConsole())
        {
//            通知文章作者
            $reply->topic->user->topicNotify(new TopicReplied($reply));
            if($users = session()->pull('at_users'))
            {
                foreach ($users as $user)
                {
                    User::find($user['id'])->topicNotify(new TopicReplied($reply, $type='reply'));
                }
            }
        }
    }

    public function deleted(Reply $reply)
    {
        $reply->topic->updateReplyCount();
    }
}

