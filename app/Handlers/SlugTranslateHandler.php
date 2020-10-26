<?php
namespace App\Handlers;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    private $appid;
    private $key;
    private $salt;
    private $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';

    public function __construct()
    {
        $this->appid = config('services.baidu_translate.appid');
        $this->key = config('services.baidu_translate.key');
        $this->salt = time();
    }

    public function translate($text, $from = 'zh', $to = 'en')
    {
        if(empty($this->appid) || empty($this->key))
        {
            return $this->pinyin($text);
        }

        $http = new Client();

        $query = http_build_query([
            'q' => $text,
            'from'  =>  $from,
            'to'    =>  $to,
            'appid' =>  $this->appid,
            'salt'  =>  $this->salt,
            'sign'  =>  md5($this->appid. $text. $this->salt. $this->key)
        ]);

        $response = $http->get($this->api.$query);

        $result = json_decode($response->getBody(), true);

        if($result['trans_result'][0]['dst'])
        {
            return Str::slug($result['trans_result'][0]['dst']);
        } else {
            return $this->pinyin($text);
        }
    }

    public function pinyin($text)
    {
        return Str::slug(app(Pinyin::class)->permalink($text));
    }
}
