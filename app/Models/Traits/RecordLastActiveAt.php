<?php
namespace App\Models\Traits;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

trait RecordLastActiveAt{
//   缓存设置
    protected $hash_prefix = 'larabbs_last_actived_at';
    protected $field_prefix = 'user_';

    public function recordLastActiveAt()
    {
//        获取今天日期
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

//        字段名称，如 user _1
        $field = $this->getHashField();

//        dd(Redis::hGetAll($hash));
//        当前时间
        $now = Carbon::now()->toDateTimeString();
//        数据写入 redis ，字段已存在会被更新
        Redis::hSet($hash, $field, $now);
    }

    public function syncUserActivedAt()
    {
//        获取昨天的日期 重新拼接哈希表名
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
//         从哈希表中获取所有数据
        $dates = Redis::hGetAll($hash);
//        遍历并保存
        foreach($dates as $user_id => $actived_at)
        {
//            将user_1转换为1
            $user_id = str_replace($this->field_prefix, '', $user_id);
//            只有当用户存在时才写入最后登录时间
            if($user = $this->find($user_id))
            {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }
//        数据已同步，删除redis哈希表
        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        $field = $this->getHashField();
        $datetime = Redis::hGet($hash, $field) ?: $value;
        if($datetime)
        {
            return new Carbon($datetime);
        } else {
            return $this->created_at;
        }
    }

    protected function getHashFromDateString($date)
    {
//        Redis 哈希表名 例如 larabbs_last_actived_at_2017-10-21
        return $this->hash_prefix. $date;
    }

    protected function getHashField()
    {
        return $this->field_prefix . $this->id;
    }
}
