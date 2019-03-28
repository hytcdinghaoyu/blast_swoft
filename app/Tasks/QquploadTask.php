<?php

namespace App\Tasks;

use App\Constants\RedisKey;
use App\Models\TencentAccess;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Swoft\Console\Helper\ConsoleUtil;
use Swoft\Task\Bean\Annotation\Task;
use Swoft\Task\Bean\Annotation\Scheduled;

/**
 * Sync task
 *
 * @Task("Qqupload")
 */

class QquploadTask
{
    

    const BLPOP_TIMEOUT = 10;   //redis超时等待

    public $host = 'http://msdk.qq.com/profile/qqscore_batch/';
    public $encode = 2;
    public $appid = 1105972533;
    public $msdkkey = 'b8ab8cc9b3de76dcc8681797e5dbbdc9';
    public $concurrency = 1;  //并发数
    
    public function QQUpload()
    {
        ConsoleUtil::log('Start ' . __FUNCTION__);
        $client = new Client();

        $requests = function () {
            $redis = bean('redis');
            while (true) {
                try {
                    $result = $redis->blpop(array(RedisKey::QQ_DATA_UPLOAD), self::BLPOP_TIMEOUT);
                    if (!empty($result)) {
                        $data = json_decode($result[1], true);
                        $url = $this->getUrl($data['openid']);

                        $data['appid'] = (string)$this->appid;

                        $t_a = bean(TencentAccess::class)->findOne($data['openid']);
                        if (!property_exists($t_a, 'openKey')) {
                            continue;
                        }
                        $data['accessToken'] = $t_a->openKey;

                        yield new Request('POST', $url, ['Content-Type' => 'application/x-www-form-urlencoded'],
                            json_encode($data));
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        };

        $pool = new Pool($client, $requests(), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $index) {
                // 成功的响应。
            },
            'rejected' => function ($reason, $index) {
                // 失败的响应
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
    
        ConsoleUtil::log('End ' . __FUNCTION__);
    }

    public function getUrl($openid)
    {
        $time = time();
        $args = [
            'timestamp' => $time,
            'appid' => $this->appid,
            'sig' => md5($this->msdkkey . $time),
            'openid' => $openid,
            'encode' => $this->encode
        ];
        return $this->host . '?' . http_build_query($args);
    }
}