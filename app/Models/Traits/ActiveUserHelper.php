<?php
namespace App\Models\Traits;
use App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

trait ActiveUserHelper{
    protected $users = [];
//  配置信息
    protected $topic_weight = 4;  // 话题权重
    protected $reply_weight = 1;  // 回复权重
    protected $pass_days    = 7;  // 多少天以内发布的内容
    protected $user_number  = 6;  // 取出的用户数量

//    缓存相关配置
    protected $cache_key = 'larabbs_active_users';
    protected $cache_expire_in_seconds = 65*60;

    public function getActiveUsers()
    {
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function (){
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
//        取得活跃用户列表
        $active_users = $this->calculateActiveUsers();
//        加入缓存
        $this->cacheActiveUsers($active_users);
    }

    private function calculateActiveUsers()
    {
//        计算发帖得分
        $this->calculateTopicScore();
//        计算回帖得分
        $this->calculateReplyScore();

//      数组按照得分排序
        $users = Arr::sort($this->users, function ($user){
            return $user['score'];
        });
//      翻转数组，高分的排名高
        $users = array_reverse($users, true);
//      取设定的人数位
        $users = array_slice($users, 0, $this->user_number, true);
//      创建空合集
        $active_users = collect();
//      将用户循环放入合集中
        foreach($users as $user_id => $user)
        {
            $user = $this->find($user_id);
            if($user)
            {
                $active_users->push($user);
            }
        }

        return $active_users;
    }

    private function calculateTopicScore()
    {
//        获取7天内发帖用户，topic_count 统计发帖数量
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                ->groupBy('user_id')->get();
        foreach($topic_users as $value)
        {
//            将发帖得分写入$users
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
        }
    }

    private function calculateReplyScore()
    {
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')->get();

        foreach($reply_users as $value)
        {
//            计算回帖数积分
            $reply_score = $value->reply_count * $this->reply_weight;
//            如果这个用户也在7天内发过帖子
            if(isset($this->users[$value->user_id]))
            {
//                将发帖积分与回复积分相加
                $this->users[$value->user_id]['score'] += $reply_score;
            } else {
                $this->users[$value->user_id]['score'] = $reply_score;
            }
        }
    }

    private function cacheActiveUsers($active_users)
    {
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_seconds);
    }
}
