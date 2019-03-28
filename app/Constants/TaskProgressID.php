<?php

namespace App\Constants;

class TaskProgressID extends BasicEnum
{

    //通关数
    CONST LEVEL_PASS = 1001;

    //关前道具使用;购买数
    CONST ITEM_PRE_USE = 2001;
    CONST ITEM_PRE_BUY = 2002;
    //关内道具使用;购买;合成数
    CONST ITEM_LEVEL_USE = 2003;
    CONST ITEM_LEVEL_BUY = 2004;
    CONST ITEM_LEVEL_GEN = 2005;

    //任意道具的使用,购买数
    CONST ITEM_USE = 2006;
    CONST ITEM_BUY = 2007;

    //元素收集
    CONST ELEMENT_GEN_BIRD_RED = 3001;
    CONST ELEMENT_GEN_BIRD_WHITE = 3002;
    CONST ELEMENT_GEN_BIRD_BLACK = 3003;
    CONST ELEMENT_GEN_BIRD_BLUE = 3004;
    CONST ELEMENT_GEN_BIRD_ORANGE = 3005;
    CONST ELEMENT_GEN_BIRD_GREEN = 3006;
    CONST ELEMENT_GEN_EGG = 3007;
    CONST ELEMENT_GEN_PIG = 3008;

    //城建任务; 皮肤获取; 皮肤购买
    CONST GARDEN_QUEST = 4001;
    CONST GARDEN_SKIN_GEN = 4002;
    CONST GARDEN_SKIN_BUY = 4003;

    //商店购买次数;购买金额
    CONST SHOP_BUY_GOLD_COUNT = 5001;
    CONST SHOP_BUY_GOLD_SUM = 5002;
    CONST SHOP_BUY_SILVER_COUNT = 5003;
    CONST SHOP_BUY_SILVER_SUM = 5004;

    //体力购买;赠送;请求;接收
    CONST LIFE_BUY = 6001;
    CONST LIFE_SEND = 6002;
    CONST LIFE_REQUIRE = 6003;
    CONST LIFE_RECEIVE = 6004;

    //好友数;拜访好友次数;分享好友次数;邀请好友次数
    CONST FRIEND_NUM = 7001;
    CONST FRIEND_VISIT = 7002;
    CONST FRIEND_SHARE = 7003;
    CONST FRIEND_INVITE = 7004;

    //持续签到;累计签到次数
    CONST SIGN_CONTINUOUS = 8001;
    CONST SIGN_TOTAL = 8002;

    //完成日常/每周任务数目
    CONST DAILY_TASK_NUM = 8003;
    CONST WEEK_TASK_NUM = 8004;

    //参加周赛次数
    CONST WEEKLY_MATCH_NUM = 8005;
}