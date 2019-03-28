<?php
namespace App\Constants;


final class MailMsgType extends BasicEnum {
    
    //系统奖励
    const SYSTEM_REWARD = 0;
    
    //向好友请求体力
    const REQUEST_LIVES = 1;
    
    //赠送体力值
    const SEND_LIVES = 2;
    
    //请求碎片
    const REQUEST_PIECE = 3;
    
    //赠送碎片
    const SEND_PIECE = 4;
    
    //请求加好友
    const REQUEST_ADD_FRIENDS = 5;

    //礼包中心
    const GIFT_CENTER = 6;
    
    //通过IDIP发送邮件
    const IDIP_MAIL = 7;
    
    //通过IDIP发送强制弹出的消息
    const IDIP_FORCE_MESSAGE = 8;

    //全服邮件,不存在redis里，作为一个标志
    const GLOBAL_MAIL = 9;
    


}