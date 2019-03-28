<?php

/**
 * Created by PhpStorm.
 * User: artist
 * Date: 2018/6/12
 * Time: 上午10:58
 */

namespace App\Constants;

class RedisKey
{
    //好友信息
    const FRIENDS_INFO = 'friends_info:%s';

    //玩家基础信息
    const PLAYER_INFO = 'player_info';

    //实时在线心跳包
    const ONLINE_CNT = 'online_cnt';

    //在线时间
    const ONLINE_TIME = 'online_time:%s:%s';//uid,Ymd

    //入队列,记录要进行的操作和数据
    const SYNC_DB_TO_REDIS = 'sync_db_to_redis';

    const USER_BIND = 'user_bind:%s';

    //从缓存中获取密钥
    const USER_TOKEN = 'user_token:%s';

    //获取中心用户的数据
    const USER_THIRD_ID = 'user_third_id:%s';

    //签到重置时间
    const SIGN_RESET_TIME = 'sign_reset_time:%s';

    const SIGN_CONFIG_ID = 'sign_config_id:%s';

    const CENTER_BOARD = 'center_board';

    const USER_SIGN_CONFIG = 'user_sign_config:%s';

    //标记登录时间
    const USER_LAST_LOGIN = 'user_last_login';

    //领取结束奖励后结束活动
    const NEW_USER_REWARD = 'new_user_reward:%s';

    const NEW_USER_REWARD_SCORE = 'new_user_reward_score';

    //找到下一个活动的信息
    const GET_NEXT_INFO = 'next_info:%s:%s';
    /**
     * Taskcontroller
     */
    //获得完成主线任务
    const MASTER_FINISH = 'master_finished:%s';

    //获得完成主线任务
    const DAILY_TASK = 'daily_task:%s:%s';

    //获得完成主线任务
    const DAILY_FINISH = 'daily_finished:%s:%s';

    //获取设置的时间偏移量
    const SIGNUP_OFFSET = 'signup_offset:%s';

    //获取签到
    const SIGN_IN = 'signed_in:%s';

    //无限生命
    const INFINITE_LIFE = 'infinite_life:%s';

    //体力
    const PHYSICAL_STRENTH = 'physical_strenth:%s:%s';

    //任务进度
    const TASK_PROGRESS = 'task_progress:%s';

    //观看次数
    const VIDEO_AFTER_WATCH = 'video_after_watch:%s';




    /**
     * ShopConroller
     *
     */

    //发放入场券
    const VOUCHER = 'voucher:%s:%s';

    //记录城建
    const CONSTRUCTION = 'construction:%s';





    /**
     * SCOREConroller
     *
     */

    //记录消除消息
    const ELIMINATE_MESSAGES ='eliminate_message:%s:%s';

    //获取新好友
    const GET_NEW_FRIENDS = 'get_new_friends:%s';

    /**
     * RewardPackageConroller
     *
     */
    //获得所有奖励包
    const REWARD_BAG = 'reward_package:%s';




    /**
     * NotificationConroller
     *
     */
    //获取生效的通知
    const EFFECTIVE_NOTICE = 'effective_notice:%s:%s:%s';

    //添加id到缓存
    const ACTIVE_ID = 'active_id:%s:%s:%s';
    
    //获取冷却时间点
    const COOLING_TIME = 'cooling_time:%s';

    //领取小鸟奖励的时间
    const BIRD_AWARD = 'bird_award:%s';



    /**
     * FriendsController
     *
     *
     */
    //获取自己的请求列表
    const OWN_LIST = 'owb_list:%s';

    //直接赠送生命，每天第一次登陆游戏时弹窗赠送生命
    const GIVE_LIFE = 'give_life:%s:%s';

    //赠送好友生命
    const GIVE_FRIEND_LIFE = 'give_friend_life:%s';

    //全局索引
    const REQUEST_LIVES_INDEX = 'request_lives_index';

    //好友请求生命
    const ASK_FOR_LIFE = 'ask_for_life:%s';

    //友谊生活
    const SEND_OUT = 'send_out:%s:%s';

    //直接发送
    const DIRECTLY_LIMIT = 'directly_limit:%s';

    //限制发送
    const REQUEST_SEND_LIMIT = 'request_send_limit:%s';

    //接收限制
    const RECEIVE_LIMIT = 'receive_limit:%s';


    //每日推荐好友次数
    const DAILY_RECOMMEND_NUM =  'daily_recommend_num:%s:%s';//uid,Ymd





    /**
     * FamilyController
     *
     *
     */

    //领取奖励
    const RECEIVE_REWARD = 'receive_reward:%s:%s';





    /**
     * DataMigrationController
     *
     */
    //获取签到的redis
    const GET_SIGN_REDIS = 'sign_redis';

    //礼物
    const GET_GIFT = 'gift:%s:%s';

    //礼物时间
    const GET_GIFT_TIME = 'gift_time:%s:%s';




    /**
     * DailyMissionController
     *
     *
     */
    //清除猪
    const ELIMINATE_PIG = 'eliminate_pig:%s:%s:%s';




    /**
     * BulletinBoardController
     *
     *none
     */




    /**
     *
     * modules/cloud
     * LeaderBoardController
     */
    //排行榜分数
    const RANKINGS = 'leaderboard:%s';

    //app排行榜列表
    const APP_LIST = 'app_list';


    /**
     *
     * API/base
     * Application
     *
     */

    //防重发
    const REQ = 'req:%s:%s:%s';

    /**
     * UserController
     */
    //用户预约
    const APPOINT =  'appoint';

    //排行榜redis名
    const LEAGUE_RANKING = "league_ranking:%s";

    //获取用户piggyTower本赛季宝箱奖励已领取列表
    const LEAGUE_PIGGYTOWER_GETTED_LIST = "league_piggytower_getted_list:%s:%s";

    //获取用户当前赛季，达到某排名的宝箱奖励的标记key
    const LEAGUE_REACH_RANKING = "league_reach_ranking:%s:%s:%s";

    //获取用户当前赛季，达到某关卡的奖励的标记key
    const LEAGUE_REACH_LEVEL = "league_reach_level:%s:%s";

    //getRankRewardTagKey
    const LEAGUE_RANK_REWARD_TAG = "league_rank_reward_tag:%s:%s";

    //getLevelInfoKey
    const LEAGUE_LEVEL_INFO = "league_level_info:%s:%s";

    //toplevel的key
    const LEAGUE_TOP_LEVEL = "league_top_level:%s:%s";

    //getLastSeasonKey
    const LEAGUE_LAST_SEASON = "league_last_season:%s";

    //得到当前用户的redis存储的key
    const LEAGUE_RANK_CUR = "league_rank_cur:%s:%s";

    //getRankKey
    const LEAGUE_RANKING_GROUP = "league_ranking_group:%s:%s:%s";

    //getLevelRankKey
    const LEAGUE_LEVEL_RANKING = "league_level_ranking:%s:%s";

    //getGoldenLevelKey
    const LEAGUE_GOLDEN_LEVEL = "league_golden_level:%s:%s";

    //getRankDanAllListKey
    const LEAGUE_ALL_USER_LIST = "league_all_user_list:%s:%s:%s";

    //getUserGroupRankKey 青铜白银，有钱和没钱用户分开分组
    const LEAGUE_USER_GROUP_RANK_MONEY = "league_group_money:%s:%s:%s:%s";

    //getUserGroupRankKey
    const LEAGUE_USER_GROUP_RANK = "league_group_rank:%s:%s:%s";

    //getUserGroupInfoKey
    const LEAGUE_USER_GROUP_INFO = "league_group_info:%s:%s";

    //存储随机生成的机器人的名字
    const LEAGUE_BOT_NAME = "league_bot_name:%s";// groupNumber

    /**
     * hatch
     */
    //用户hatch活动每个赛季领取奖励的列表exp:[2=>0,3=>1] key代表关卡数 ，value 0未领取,1已领取
    const HATCH_USER_REWARD = "hatch_user_reward:%s:%s";//$seasonName  $uuid

    //用户hatch活动每个赛季进度，存的是活动中的关卡进度
    const HATCH_USER_PROGRESS = "hatch_user_progress:%s:%s";//$seasonName  $uuid

    
    /**
     * 任务进度
     */
    const TARGET_PROGRESS = "targetProgress:%s:%s:%s";
    
    const TARGET_EXTRA = "targetExtra:%s";
    
    const TARGET_REWARD = "targetReward:%s:%s:%s";
    
    const FIRST_LOGIN_HOUR = "first_login_hour:%s:%s";
    
    const DAILY_ACTIVE_ISSET = "daily_active_isset:%s:%s";

    /**
     * 拼图活动
     */
    const PUZZLE_KEY = "puzzle:%s:%s";
    const PUZZLE_COMPOSE_TIMES = "puzzle_compose_times:%s:%s";
    const PUZZLE_MESSAGE_KEY = "puzzle_message:%s:%s:%s";
    
    const PUZZLE_SEND_TIMES = "puzzle_send_times:%s:%s";
    const PUZZLE_REQUEST_TIMES = "puzzle_request_times:%s:%s";
    const PUZZLE_RECEIVE_TIMES = "puzzle_receive_times:%s:%s";

    /**
     * rule从json文件读取
     */
    const HOT_UPDATE_PROCESSED = "hot_update_processed";

    /**
     * friendReward
     */

    const SHARE_REWARD_RECORD = "share_reward_record:%s:%s:%s";//string型  shareType  20180801 $uid
    //用户friend领取奖励的列表exp:[3=>1,6=>1] key代表邀请次数 ，value值为null代表未领取,1已领取
    const INVITE_REWARD_RECORD = "invite_reward_record:%s";//散列型  $uid

    const SEND_INVITE_DAILY_NUM = "send_invite_daily_num:%s:%s";//20180801  $uid

    const INVITE_SUCCESS_NUM = "invite_success_num:%s";//  $uid



    /**
     * 注册uid
     */

    const CURRENT_UID_INC = "current_uid_inc";


    /**
     * 平台特权
     */
    const PLATFORM_START_VIP = "platform_start_vip:%s:%s";

    /**
     * 商店购买限制
     */
    const SHOP_BUY_COUNTS = "shop_buy_counts:%s";

    /**
     * 礼包中心
     */
    const GIFT_CENTER_RECORD = "gift_center_record:%s:%s:%s:%s";//uid dateType giftName date

    /**
     * qq会员礼包中心(h5上领取)
     */

    //领取记录
    const QQVIP_GIFT_RECORD = "qqvip_gift_record:%s:%s:%s:%s";//uid dateType giftName date
    
    //上报数据
    const QQ_DATA_UPLOAD = "qq_data_upload";

    //禁止参与繁荣度所有人
    const BAN_PROSPERITY = "ban_prosperity";
}