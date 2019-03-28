<?php

namespace App\Constants;

/**
 * 错误码类
 *
 * @author caikang
 */
class Message
{
    
    const SUCCESS = 1;
    
    /**
     * 系统验证阶段错误码
     */
    const SYSTEM_PARAM_EMPTY = 10000;
    
    const SYSTEM_JASON_FORMAT_ERROR = 10001;
    
    const SYSTEM_UNKNOWN_PARAM = 10002;
    
    const SYSTEM_ERROR_PARAM = 10003;
    
    const SYSTEM_DECRYPT_ERROR = 10004;
    
    const SYSTEM_DATA_ERROR = 10005;
    
    const SYSTEM_TOKEN_ERROR = 10006;
    
    const SYSTEM_USER_NOT_FOUND = 10007;
    
    const SYSTEM_APP_NOT_FOUND = 10008;
    
    const SYSTEM_REQUEST_RESEND = 10009;
    
    const DECRYPT_DATA_ERROR = 20001;
    
    const JSON_DECODE_ERROR = 20002;
    
    /**
     * 用户相关错误码
     */
    const USER_REG_FAILED = 10100;
    
    const USER_BIND_FAILED = 10101;
    
    const USER_UPDATE_FORBIDDEN_FIELD = 10102;
    
    const USER_UPDATE_UNKNOWN_ERROR = 10103;
    
    const USER_OPENID_CANNOT_EMPTY = 10104;
    
    const USER_NICKNAME_SENSITIVE = 10105;
    
    const USER_NOT_FOUND = 10106;
    
    const USER_NOT_SPECIAL = 10107;
    
    const USER_INFO_NOT_FOUND = 10108;
    
    /**
     * 排行榜相关错误码
     */
    const LEADERBOARD_NOT_FOUND = 10200;
    
    const LEADERBOARD_REDIS_DISABLED = 10201;
    
    /**
     * 热更新相关错误码
     */
    const HOTUPDATE_RULE_ERROR = 10300;
    
    /**
     * global注册登录
     */
    const THIRD_OR_PLATFORM_NOT_EXIST = 31001;
    
    const CREATE_USER_FAIL = 31003;
    
    const UUID_NOT_EXIST = 31004;
    
    const USER_INFO_EXISTED = 31005;
    
    /**
     * 新手活动
     */
    const INVALID_REWARD_ID = 32001;
    const SCORE_NOT_ENOUGH = 32002;
    const HAS_REWARDED = 32003;
    
    /**
     * 周赛dailymission
     *
     */
    const LIFE_NOT_ENOUGH_DAILYMISSION = 32101;
    
    const MONEY_NOT_ENOUGH_DAILYMISSION = 32102;
    
    const WRONG_DAILYMISSION_TOKEN = 32103;
    
    const WRONG_TIME_DAILYMISSION = 32104;
    
    
    /*
     * 签到
     */
    const SIGNED_TODAY = 31305;
    const REDO_SIGN_FAIL = 31306;
    
    /**
     * hatch
     */
    const HATCH_REWARD_FAIL = 31405;
    
    /**
     * 任务进度
     */
    const TARGET_ID_INVALID = 33001;
    const TASK_TYPE_INVALID = 33002;
    const TASK_ID_NOT_FOUND = 33003;
    const TASK_ID_REWARDED = 33004;
    const TARGET_NOT_ENOUGH = 33005;
    
    /**
     * piece
     */
    const PIECE_ID_INVALID = 34001;
    const PIECE_NUMBER_TYPE_ERROR = 34002;
    const MUST_HAVE_ALL_PIECES = 34003;
    const PIECE_NUMBER_NOT_ENOUGH = 34004;
    const PIECE_SEND_NUM_LIMITED = 34005;
    const PIECE_REQUEST_NUM_LIMITED = 34006;
    const PIECE_RECEIVE_NUM_LIMITED = 34007;
    
    /**
     * 好友系统领取奖励
     */
    const FRIEND_REWARD_GOTTEN = 35501;
    const FRIEND_REWARD_PROGRESS_INVALID = 35502;
    const INVALID_SHARE_REWARD_TYPE = 35503;
    const RECOMMEND_FRIEND_UID_INVALID = 35504;
    const RECOMMEND_FRIEND_SEND_NUM_LIMITED = 35505;
    const SUM_FRIEND_SEND_NUM_LIMITED = 35506;
    
    /**
     * 日常任务
     */
    const DAILY_MISSION_REQUEST_NUM_LIMITED = 36001;
    
    /**
     * 主线过关$level参数限制
     */
    const LEVEL_REQUEST_INVALID = 37001;
    
    /**
     * 支付相关
     */
    const INVALID_PRODUCT_ID = 38001;
    const DIAMOND_NOT_ENOUGH = 38002;
    
    /**
     * 在线心跳包获取惩罚信息
     */
    const PUNISH_INFO_ERROR = 39001;//服务器获取数据错误
    
    const OFF_LINE = 39002;//需要强制下线
    
    const BAN_USER_LOGIN = 39003;//被封号无法登陆
    
    /**
     * 惩罚接口返回错误码
     */
    const BAN_RANKING_STATUS = 39101;//获取排行榜数据时禁止获取
    
    
    /**
     * 观看广告奖励错误码
     */
    const INVALID_REWARD_NAME = 39201;//奖励名称错误
    
    const REPEATED_AD_REWARD = 39202;//奖励重复
    
    
    /**
     * 获取错误码对应的翻译
     */
    public static function getMessage($code)
    {
        return isset(static::messagePacket()[$code]) ? static::messagePacket()[$code] : 'msg not found';
    }
    
    /**
     * 错误码翻译包
     */
    private static function messagePacket()
    {
        return [
            static::SUCCESS => 'success',
            static::SYSTEM_PARAM_EMPTY => 'post data can not be empty!',
            static::SYSTEM_JASON_FORMAT_ERROR => 'post data\'s json format error!',
            static::SYSTEM_UNKNOWN_PARAM => 'there is unknown param in your post data!',
            static::SYSTEM_ERROR_PARAM => 'there is error param in your post data!',
            static::SYSTEM_DECRYPT_ERROR => 'occured error when decrypy data!',
            static::SYSTEM_DATA_ERROR => 'the param \'data\' in your post data is error!',
            static::SYSTEM_TOKEN_ERROR => 'occured error when validate your accessTocken!',
            static::SYSTEM_USER_NOT_FOUND => 'the uuid not found in database!',
            static::SYSTEM_APP_NOT_FOUND => 'the app info not found in database!',
            static::USER_REG_FAILED => 'unkown error when user reg!',
            static::USER_BIND_FAILED => 'unkown error when user login!',
            static::SYSTEM_REQUEST_RESEND => 'your request is illegal!',
            static::LEADERBOARD_NOT_FOUND => 'wrong leaderBoard type!',
            static::LEADERBOARD_REDIS_DISABLED => 'leaderboard redis disabled, please try again later!',
            static::USER_UPDATE_FORBIDDEN_FIELD => 'the field updating is not allowed!',
            static::USER_UPDATE_UNKNOWN_ERROR => 'unknown error when update user info!',
            static::USER_NOT_SPECIAL => 'user not in special list',
            static::USER_INFO_NOT_FOUND => 'user info not found!',
            
            static::HOTUPDATE_RULE_ERROR => 'the rules format error!',
            static::USER_OPENID_CANNOT_EMPTY => 'openid can not be empty!',
            static::USER_NICKNAME_SENSITIVE => 'user nickname sensitive!',
            static::USER_NOT_FOUND => 'user not found!',
            static::DECRYPT_DATA_ERROR => 'decrypt data error',
            
            static::THIRD_OR_PLATFORM_NOT_EXIST => 'thirdName or platform name not exist!',
            static::CREATE_USER_FAIL => 'global create center user failed!',
            static::UUID_NOT_EXIST => 'global uuid not exist!',
            static::USER_INFO_EXISTED => 'user info existed',
            
            static::INVALID_REWARD_ID => 'Invalid reward_id!',
            static::SCORE_NOT_ENOUGH => 'Score is not enough!',
            static::HAS_REWARDED => 'The reward has been received!',
            
            static::SIGNED_TODAY => 'you have signed this day',
            static::REDO_SIGN_FAIL => 'redo sign failed',
            
            static::HATCH_REWARD_FAIL => 'you can not get reward',
            
            static::TARGET_ID_INVALID => 'target id invalid',
            static::TASK_TYPE_INVALID => 'task type invalid, 0:main,1:daily,2:weekly',
            static::TASK_ID_NOT_FOUND => 'task id not found',
            static::TASK_ID_REWARDED => 'task id rewarded',
            static::TARGET_NOT_ENOUGH => 'target not enough',
            
            static::PIECE_ID_INVALID => 'piece id invalid',
            static::PIECE_NUMBER_TYPE_ERROR => 'piece number type error',
            static::MUST_HAVE_ALL_PIECES => 'must have all pieces',
            static::PIECE_NUMBER_NOT_ENOUGH => 'piece number not enough',
            static::PIECE_SEND_NUM_LIMITED => 'daily send num exceeded',
            static::PIECE_REQUEST_NUM_LIMITED => 'daily request num exceeded',
            static::PIECE_RECEIVE_NUM_LIMITED => 'daily receive num exceeded',
            
            static::FRIEND_REWARD_GOTTEN => 'you have already gotten reward',
            static::FRIEND_REWARD_PROGRESS_INVALID => 'the progress is invalid',
            static::INVALID_SHARE_REWARD_TYPE => 'the shareType is invalid',
            static::RECOMMEND_FRIEND_UID_INVALID => 'the friendUid is invalid',
            static::RECOMMEND_FRIEND_SEND_NUM_LIMITED => 'the number of friends added in oneday exceeds the upper limit',
            static::SUM_FRIEND_SEND_NUM_LIMITED => 'the number of friends added in game exceeds the upper limit',
            
            static::DAILY_MISSION_REQUEST_NUM_LIMITED => 'dailyMission request num exceeded',
            
            static::LEVEL_REQUEST_INVALID => 'the param of level is invalid ',
            
            static::DIAMOND_NOT_ENOUGH => 'diamond not enough',
            static::INVALID_PRODUCT_ID => 'invalid product id',
            
            static::PUNISH_INFO_ERROR => 'the response of punishInfo is error',
            static::OFF_LINE => 'user must re-login',
            static::BAN_USER_LOGIN => ' user has been banned',
            
            static::LIFE_NOT_ENOUGH_DAILYMISSION => 'No life,can not play daily mission!',
            static::MONEY_NOT_ENOUGH_DAILYMISSION => 'do not have enough gold',
            static::WRONG_DAILYMISSION_TOKEN => 'Wrong dailymissonToken!',
            static::WRONG_TIME_DAILYMISSION => 'Wrong time,can not play daily mission!',
            
            static::BAN_RANKING_STATUS => 'You can\'t participate in the ranking list now.',
            
            static::INVALID_REWARD_NAME => 'the name of AdReward is invalid',
            static::REPEATED_AD_REWARD => 'you have already gotten AdReward'
        ];
    }
}