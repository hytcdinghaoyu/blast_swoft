<?php

namespace App\datalog;
use App\Models\Dao\UserInfoDao;
use yii\helpers\ArrayHelper;

class AccountFlow extends  DataLog
{

    public $actionType = null;//idip接口功能更新后可以代表后台添加的金银币（1||2）

    public $num = 0;

    public $flowAction = null;

    public $detail = '';

    public $gold = 0;

    public $silver = 0;

    function rules()
    {
        $rules = [
            [['actionType', 'flowAction', 'num'], 'required'],
            [['actionType', 'flowAction', 'num'], 'integer'],
        ];
        return ArrayHelper::merge(parent::rules(), $rules);

    }

    /**
     * @param $type
     * @param $num
     * @param $action integer 操作的分类
     * @param string $detail
     * @return bool
     */
    public static function newFlow($type, $num, $action, $detail = '')
    {
        $globalInfo = globalInfo();
        $userInfo = bean(UserInfoDao::class)->findOneByUid($globalInfo->getUid());

        $accountFlow = new self();
        $accountFlow->actionType = (int)$type;
        $accountFlow->num = $num;
        $accountFlow->flowAction = $action;
        $accountFlow->detail = $detail;
        $accountFlow->gold = empty($userInfo) ? 0 : $userInfo->getGoldCoin();
        $accountFlow->silver = empty($userInfo) ? 0 :$userInfo->getSilverCoin();
        $accountFlow->send();
    }

}