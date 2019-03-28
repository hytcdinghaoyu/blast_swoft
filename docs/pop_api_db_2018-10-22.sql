# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.22)
# Database: pop_api_db
# Generation Time: 2018-10-22 06:04:19 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table family_birds
# ------------------------------------------------------------

DROP TABLE IF EXISTS `family_birds`;

CREATE TABLE `family_birds` (
  `uid` bigint(20) unsigned NOT NULL,
  `birdId` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `show` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`,`birdId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';



# Dump of table family_quest
# ------------------------------------------------------------

DROP TABLE IF EXISTS `family_quest`;

CREATE TABLE `family_quest` (
  `uid` bigint(20) unsigned NOT NULL,
  `questId` varchar(50) NOT NULL,
  `step` varchar(100) NOT NULL DEFAULT '',
  `own_steps` varchar(100) NOT NULL DEFAULT '',
  `show` tinyint(4) NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `draw` tinyint(4) NOT NULL DEFAULT '0',
  `init_play` tinyint(4) NOT NULL DEFAULT '0',
  `s_time` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`,`questId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';



# Dump of table item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `item`;

CREATE TABLE `item` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID，用来识别用户数据',
  `item` smallint(5) unsigned NOT NULL COMMENT '道具ID',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '道具数量',
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`,`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';



# Dump of table league
# ------------------------------------------------------------

DROP TABLE IF EXISTS `league`;

CREATE TABLE `league` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL COMMENT '用户uid',
  `upDown` tinyint(4) NOT NULL DEFAULT '0' COMMENT '相对于上赛季上升或下降',
  `rank` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '段位',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间：时间戳',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间：时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';



# Dump of table my_friends
# ------------------------------------------------------------

DROP TABLE IF EXISTS `my_friends`;

CREATE TABLE `my_friends` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID，用来识别用户数据',
  `fuid` bigint(20) unsigned NOT NULL COMMENT '好友的uid',
  `updated_at` int(11) unsigned DEFAULT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`,`fuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';



# Dump of table order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `order`;

CREATE TABLE `order` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `billno` varchar(100) NOT NULL DEFAULT '' COMMENT '订单号，全局唯一',
  `money` int(11) NOT NULL DEFAULT '0' COMMENT 'rmb金额变动数',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '代币金额变动数',
  `product_id` varchar(50) NOT NULL DEFAULT '' COMMENT '礼包id',
  `pay_item` varchar(255) NOT NULL DEFAULT '' COMMENT '购买的道具礼包详情',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';



# Dump of table score
# ------------------------------------------------------------

DROP TABLE IF EXISTS `score`;

CREATE TABLE `score` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID，用来识别用户数据',
  `level` smallint(5) unsigned NOT NULL COMMENT '关卡',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '得分',
  `star` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '星级',
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`,`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';



# Dump of table task
# ------------------------------------------------------------

DROP TABLE IF EXISTS `task`;

CREATE TABLE `task` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID，用来识别用户数据',
  `task_id` int(10) unsigned NOT NULL COMMENT '任务ID',
  `reward_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务完成时获取任务奖励的时间，未获取完成的任务为0',
  `updated_at` int(11) unsigned DEFAULT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';



# Dump of table user_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_info`;

CREATE TABLE `user_info` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID，用来识别用户数据',
  `silver_coin` int(10) unsigned NOT NULL COMMENT '银币',
  `gold_coin` int(10) unsigned NOT NULL COMMENT '金币',
  `lives` smallint(6) unsigned NOT NULL COMMENT '活力值',
  `last` bigint(20) unsigned NOT NULL COMMENT '上次活力值的更新时间',
  `new_level` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '关卡',
  `stars` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总星星数量',
  `remainStars` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余星星数',
  `avatar` varchar(255) NOT NULL DEFAULT '1' COMMENT '头像图标',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `population` int(11) NOT NULL DEFAULT '0' COMMENT '人口数',
  `prosperity` int(11) NOT NULL DEFAULT '0' COMMENT '繁荣度',
  `tencentVip` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:非会员 1:普通会员 2:超级会员',
  `save_amt` int(11) NOT NULL DEFAULT '0' COMMENT '历史充值金额（钻石数）',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0:下线玩家 1:在线正常角色 2:暂时封停角色 3:为永久封停角色',
  `last_login_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "uid"';




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
