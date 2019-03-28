<?php

namespace App\Constants;

/**
 * Class FlowActionConst
 * 物品流水一级原因
 */
final class FlowAction extends BasicEnum
{
    
    const DETAIL_FIRST_PASS_LEVEL = 1;//首次通关奖励
    const DETAIL_STAR_UPGRADE = 2;//升星奖励
    const DETAIL_PASS_LEVEL = 3;//未升星过关
    const DETAIL_TASK_REWARD = 4;//任务奖励
    const DETAIL_DAILY_TASK_REWARD = 5;//每日任务奖励
    const DETAIL_SHOP_BUY = 6;//商店购买,v1接口使用，V2拆为3个（13,14,15,30,31,32,33），统计购买地点
    const DETAIL_SHOP_RPE_BUY = 7;//关前购买
    const DETAIL_SHOP_PAY = 8;//付费购买金币（已不再使用）
    const DETAIL_SHOP_GOLD = 9;//付费购买金币
    const DETAIL_WORLD_COLLECT = 10;//大地图收集宝箱
    const DETAIL_DAILY_MISSION_COST = 11;//每日任务购买次数
    const DETAIL_GM_SEND_RES = 12;//管理员赠送
    
    const DETAIL_SHOP_BUY_WORLD = 13;//大地图商店购买
    const DETAIL_SHOP_BUY_LEVEL = 14;//关卡商店购买
    const DETAIL_SHOP_BUY_DIG = 15;//挖地商店购买
    const DETAIL_SHOP_BUY_4_STAR = 51;//4星关卡商店购买
    
    const DETAIL_DAILY_TASK_PASS_REWARD = 16;//每日任务过关奖励1000银币
    const DETAIL_PIGGY_TOWER_CHEST = 17;//piggyTower宝箱奖励
    const DETAIL_PIGGY_TOWER_RANK = 18;//piggyTower赛季结算后上赛季排名奖励
    const DETAIL_PIGGY_TOWER_LEVEL = 19;//piigyTower关卡宝箱奖励
    const DETAIL_DAILY_SIGN = 20;//每日签到奖励
    
    const DETAIL_BULLETIN_BOARD = 28;//公告板奖励
    const DETAIL_CONTINUE_FAIL_REWARD = 29;//连续失败激励包
    
    const DETAIL_CLIENT_OPTION = 28;
    
    const DETAIL_SHOP_BUY_TOWER_WORLD = 30;//piggyTower活动地图花费金币
    const DETAIL_SHOP_BUY_TOWER_BATTLE = 31;//piggyTower关卡花费金币
    const DETAIL_SHOP_BUY_OL_WORLD = 32;//一命模式活动地图花费金币
    const DETAIL_SHOP_BUY_OL_BATTLE = 33;//一命模式关卡花费金币
    const DETAIL_SHOP_BUY_TOWER_SPECIAL_WORLD = 34;//piggyTowerSpecial活动地图花费金币
    const DETAIL_SHOP_BUY_TOWER_SPECIAL_BATTLE = 35;//piggyTowerSpecial关卡花费金币
    
    const DETAIL_MT_LEAGUE_RANK = 36;//piggyTower赛季结算后上赛季排名奖励
    const DETAIL_MT_LEAGUE_LEVEL = 37;//piigyTower关卡宝箱奖励
    const DETAIL_MT_LEAGUE_CHEST = 38;//piggyTower宝箱奖励
    const DETAIL_SHOP_BUY_MT_LEAGUE_WORLD = 40;//LEAGUE活动地图花费金币
    const DETAIL_SHOP_BUY_MT_LEAGUE_BATTLE = 41;//LEAGUE关卡花费金币
    
    const ACTION_SEND_HATCH_REWARD =42;//孵化活动发送奖励
    
    
    const ACTION_ITME_LEVEL_PASS_REWARD = 101;//通关奖励的道具
    const ACTION_ITME_LEVEL_PASS_SPEND = 102;//关卡消耗的道具
    const ACTION_ITME_SHOP_BUY = 103;//购买的关中道具
    const ACTION_ITME_SHOP_PREBUY = 104;//购买的关前道具
    const ACTION_ITME_SHOP_GOLD = 105;//付费购买的礼包道具
    const ACTION_ITME_TASK_REWARD = 106;//任务奖励的道具
    const ACTION_ITME_DAILY_MISSION = 107;//每日任务消耗的道具
    const ACTION_ITME_GM_SEND = 108;//管理员赠送的道具
    
    
    const ACTION_GARDEN_EVENT_REWARD = 111;
    
    
    //城建相关
    const ACTION_QUICK_BUILD = 114;//缩短城建时间
    const ACTION_BUY_BUILD = 115;//购买城建皮肤
    
    const ACTION_BUILD_MAIN = 116;//主线获得星星
    const ACTION_BUILD_QUEST = 117;//城建任务消耗星星
    
    //好友系统奖励
    const ACTION_REWARD_SEND_INVITE = 120;//发送邀请奖励
    const ACTION_REWARD_INVITE_PROGRESS = 121;//邀请进度奖励
    const ACTION_REWARD_SHARE = 122;//分享奖励
    
    
    const NEW_USER_ACTIVITY = 301; //新手活动奖励
    
    const TASK_PROGRESS = 302;
    
    //邮件奖励
    const MAILER_REWARD = 303;
    
    //idip接口与
    const IDIP_ITEM_UPDATEITEM = 4107; //idip更改道具接口的flowAction
    
    const IDIP_ITEM_UPDATEMONEY = 4105;//idip更改货币接口的flowAction
    
    //puzzle
    const ACTION_PUZZLE_REWARD = 110;
    const DETAIL_PUZZLE_SCENE_UNKNOWN = 200;//获取碎片的场景：未知场景
    const DETAIL_PUZZLE_SCENE_COMMON = 201;//获取碎片的场景：普通关卡
    const DETAIL_PUZZLE_SCENE_DAILY = 202;//获取碎片的场景：每日任务
    const DETAIL_PUZZLE_SCENE_TOWER = 203;//获取碎片的场景：天梯任务
    const DETAIL_PUZZLE_SCENE_ONE_LIFE = 204;//获取碎片的场景：一命五关
    const DETAIL_PUZZLE_SCENE_DIFFICULT_LEVEL = 205;//获取碎片的场景：困难关卡
    const DETAIL_PUZZLE_SCENE_4_STAR = 206;//获取碎片的场景：4星关卡
    const DETAIL_PUZZLE_SCENE_QUEST = 207;//获取碎片的场景：任务系统配送
    const DETAIL_PUZZLE_SCENE_SEND = 208;//获取碎片的场景：赠送出去
    const DETAIL_PUZZLE_SCENE_RECEIVE = 209;//获取碎片的场景：获得赠送
    const DETAIL_PUZZLE_SCENE_TOWER_SPECIAL = 210;//获取碎片的场景：特别天梯任务
    const DETAIL_PUZZLE_SCENE_TOWER_REWARD = 211;//获取碎片的场景：天梯任务15关通关奖励
    const DETAIL_PUZZLE_SCENE_TOWER_SPECIAL_REWARD = 212;//获取碎片的场景：特别天梯任务15关通关奖励
    const DETAIL_PUZZLE_SCENE_CONSUME = 213;//消耗碎片的场景：合成拼图，每种碎片消耗一个
    
    const SHOP_BUY_GOODS = 8001;//商店购买道具，钻石换道具
    const MONEY_BUY_GOODS = 8002;//直购道具，rmb换道具
    
    const AD_REWARD = 401;//观看广告奖励
    
    //主线关卡促销
    const  PROMOTION_SCORE_REWARD = 501;//领取奖励
    
    const  SCORE_PROMOTION_BUY = 501;//购买主线关卡促销
    
}