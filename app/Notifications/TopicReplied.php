<?php

namespace App\Notifications;

use App\Models\Reply;
use App\Notifications\Channels\JpushChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JPush\PushPayload;

class TopicReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public $reply;
    public $type;
    public function __construct(Reply $reply, $type = 'topic')
    {
        $this->reply = $reply;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', JpushChannel::class];
    }

    public function toPush($notifiable, PushPayload $payload): PushPayload
    {
        return $payload
            ->setPlatform('all')
            ->addRegistrationId($notifiable->registration_id)
            ->setNotificationAlert(strip_tags($this->reply->content));
    }

    public function toMail($notifiable)
    {
        $url = $this->reply->topic->link(['#reply'. $this->reply->id]);
//        如果回复文章的作者和@的人相同，则发送只发送回复通知
        if($this->type == 'topic' || $this->reply->topic->user_id == $this->reply->topic->user->id){
            return (new MailMessage())->line('你的话题有新回复')->action('查看回复', $url);
        } else if($this->type == 'reply')
        {
            die;
            return (new MailMessage())->line('有人在话题中提到了你')->action('查看回复', $url);
        }
    }

    public function toDatabase($notifiable)
    {
        $topic = $this->reply->topic;
        $link = $topic->link(['#reply' . $this->reply->id]);

        return [
            'reply_id'        =>    $this->reply->id,
            'reply_body'   =>    $this->reply->body,
            'user_id'         =>    $this->reply->user->id,
            'user_name'       =>    $this->reply->user->name,
            'user_avatar'     =>    $this->reply->user->avatar,
            'topic_link'      =>    $link,
            'topic_id'        =>    $topic->id,
            'topic_title'     =>    $topic->title,
            'type'            =>    $this->type
        ];
    }
}
