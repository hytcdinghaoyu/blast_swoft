<?php

namespace App\Controllers\Api;



use App\Controllers\CommonController;
use App\Models\Dao\CenterBulletinBoardDao;
use App\Models\Dao\CenterRewardPackageDao;
use App\Models\Dao\TaskDao;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use yii\helpers\ArrayHelper;
use Swoftx\Aop\Cacheable\Annotation\Cacheable;

/**
 * 用户模块.
 * @Controller(prefix="/bulletinboard")
 */
class BulletinBoardController extends CommonController
{

    public $cacheKey = "bulletinBoard:rewardPackage:actived:all";
    public $cacheTime = 180;

    /**
     * @RequestMapping(route="list")
     * @return array
     */
    public function actionList($language = 'en')
    {
        $bulletinReward = $this->getAll();

        if (!empty($bulletinReward['bulletinBoard'])) {
            $bulletin = bean(CenterBulletinBoardDao::class)->bulletinFilter($bulletinReward['bulletinBoard']['main']);
            /**
             * @todo 过滤无效的公告
             */
            if (!empty($bulletin)) {
                $resultIds = [];
                $bulletinIds = ArrayHelper::getColumn($bulletin, 'id');
                $updateTime = ArrayHelper::getColumn($bulletin, 'updated_at');
                $sort = ArrayHelper::getColumn($bulletin, 'sort');
                $main_index = 0;
                foreach ($bulletinIds as $kBulletinId) {
                    if (isset($bulletinReward['bulletinBoard']['pair'][$kBulletinId]) && !empty($bulletinReward['bulletinBoard']['pair'][$kBulletinId])) {
                        foreach ($bulletinReward['bulletinBoard']['pair'][$kBulletinId] as $kLanguageVer) {
                            if ($kLanguageVer['language'] == $language) {
                                $resultIds [] = [
                                    $kBulletinId,
                                    $updateTime[$main_index],
                                    $kLanguageVer['updated_at'],
                                    $sort[$main_index]
                                ];
                            }
                        }
                    }
                    $main_index++;
                }


            }

        }
        return [
            'code' => 1,
            'bulletinIds' => isset($resultIds) ? $resultIds : []
        ];
    }

    /**
     * 获得单个公告详细内容
     * @RequestMapping(route="get")
     */
    public function actionGet($ids, $language = 'en')
    {
        $cache = bean('cache');
        $bulletinReward = $this->getAll();
        if (empty($bulletinReward['bulletinBoard'])) {
            $cache->delete($this->cacheKey);
            return [
                'code' => 39001,
                'message' => "bulletin or $language not found!"
            ];
        }
        $bulletins = $bulletinReward['bulletinBoard']['main'];
        $bulletinAppoint = [];

        $globalInfo = globalInfo();
        $uid = $globalInfo->getUid();
        foreach ($ids as $id) {
            $bulletin = [];
            foreach ($bulletins as $kBulletin) {
                if ($kBulletin['id'] == $id) {
                    foreach ($bulletinReward['bulletinBoard']['pair'][$id] as $kLan) {
                        if ($kLan['language'] == $language) {
                            $bulletin['main'] = $kBulletin;
                            $bulletin['pair'] = $kLan;
                            $bulletin['pair']['title_config'] = json_decode($kLan['title_config'], true);
                        }
                    }
                }
            }
            if (empty($bulletin)) {
                $cache->delete($this->cacheKey);
                return [
                    'code' => 39001,
                    'message' => "bulletin $id or $language not found!"
                ];
            }
            if ($bulletin['main']['reward_id'] != 0) {
                foreach ($bulletinReward['rewardPackage'] as $kRewardPackage) {
                    if ($kRewardPackage ['id'] == $bulletin['main']['reward_id']) {
                        $bulletin['reward_content'] = json_decode($kRewardPackage['contain_list'], true);
                        $recieveNum = $kRewardPackage['recieve_num'];
                        break;
                    }
                }
                if (!isset($bulletin['reward_content'])) {
                    $cache->delete($this->cacheKey);
                    return [
                        'code' => 39002,
                        'message' => "bulletin reward $id not found!"
                    ];
                }
                $recievedNum = bean(TaskDao::class)->getCountById($uid, $bulletin['main']['reward_id']);
                $is_recieved = $recieveNum - $recievedNum;
                $bulletin['is_recieved'] = ($is_recieved <= 0) ? 0 : $is_recieved;
            }
            $bulletinAppoint [$id] = $bulletin;
        }
        return [
            'code' => 1,
            'bulletin' => $bulletinAppoint
        ];
    }

    /**
     *
     *@Cacheable(key="bulletinBoard:all", ttl=600)
     *
     * 公告栏表与奖励包表同步缓存
     */
    private function getAll()
    {
        $resultDb = [];
        $resultDb['bulletinBoard'] = bean(CenterBulletinBoardDao::class)->getAll();
        $resultDb['rewardPackage'] = bean(CenterRewardPackageDao::class)->getAll();
        return $resultDb;

    }
}