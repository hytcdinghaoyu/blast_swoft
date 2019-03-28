# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.22)
# Database: pop_api_global_cn
# Generation Time: 2018-10-22 03:55:36 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table center_board
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_board`;

CREATE TABLE `center_board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_actived` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否是激活 0 未激活，1 已激活',
  `is_force` tinyint(4) NOT NULL DEFAULT '0',
  `language` varchar(255) NOT NULL COMMENT '语言类型',
  `title_config` text NOT NULL COMMENT '标题，一级页面标题配置 :[{"title":"wqe","position":[300,200],"size":21} ...]',
  `content` longtext NOT NULL COMMENT '公告内容',
  `extra` varchar(255) NOT NULL DEFAULT '',
  `start_at` int(11) unsigned NOT NULL COMMENT '开始时间',
  `end_at` int(11) unsigned NOT NULL COMMENT '结束时间',
  `created_at` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table center_bulletin_board
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_bulletin_board`;

CREATE TABLE `center_bulletin_board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '公告名称',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型 默认为0',
  `reward_id` int(11) NOT NULL DEFAULT '0' COMMENT '奖励id 0，无奖励',
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '跳转活动id 0,不跳转',
  `is_actived` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否被激活 0 没有 1 激活',
  `is_testing` tinyint(4) NOT NULL COMMENT '是否是测试 0 不是 ，1 是',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除 0未删除 1已删除',
  `is_forced` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否强制弹出',
  `extra` varchar(255) DEFAULT '' COMMENT '备用字段，根据不同的通知类型可能有不同的作用,比如倒计时',
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `start_at` int(11) DEFAULT '0' COMMENT '开始时间戳',
  `end_at` int(11) DEFAULT '0' COMMENT '结束时间戳',
  `version` varchar(255) DEFAULT '[]' COMMENT '允许的版本号',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table center_bulletin_language
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_bulletin_language`;

CREATE TABLE `center_bulletin_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bulletin_board_id` int(11) NOT NULL COMMENT '公告板Id',
  `is_actived` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否是激活 0 未激活，1 已激活',
  `language` varchar(255) NOT NULL COMMENT '语言类型',
  `banner` varchar(255) NOT NULL,
  `title_config` text COMMENT '标题，一级页面标题配置 :[{"title":"wqe","position":[300,200],"size":21} ...]',
  `content` longtext NOT NULL COMMENT '公告内容',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `bulletin_board_id` (`bulletin_board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table center_hatch
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_hatch`;

CREATE TABLE `center_hatch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名',
  `operator` varchar(50) NOT NULL COMMENT '发布通知的管理员',
  `start_at` int(11) NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `end_at` int(11) NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否被删除：0，未删除；1，已删除。',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `config_level` int(11) NOT NULL DEFAULT '0' COMMENT '配置的关卡',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table center_league
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_league`;

CREATE TABLE `center_league` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名',
  `operator` varchar(50) NOT NULL COMMENT '发布通知的管理员',
  `start_at` int(11) NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `end_at` int(11) NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否被删除：0，未删除；1，已删除。',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table center_puzzle
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_puzzle`;

CREATE TABLE `center_puzzle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名',
  `operator` varchar(50) NOT NULL COMMENT '发布通知的管理员',
  `start_at` int(11) NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `end_at` int(11) NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否被删除：0，未删除；1，已删除。',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table center_reward_package
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_reward_package`;

CREATE TABLE `center_reward_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '奖励礼包名称',
  `contain_list` varchar(255) NOT NULL DEFAULT '' COMMENT '礼包内容列表，json',
  `recieve_num` int(11) NOT NULL DEFAULT '1' COMMENT '每个用户领取次数，默认是1',
  `is_deleted` tinyint(4) NOT NULL COMMENT '是否已删除',
  `is_used` tinyint(4) NOT NULL DEFAULT '0' COMMENT '占用状态：0 未 使用 1 已使用',
  `created_at` int(255) NOT NULL,
  `updated_at` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table center_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_user`;

CREATE TABLE `center_user` (
  `uid` bigint(20) unsigned NOT NULL COMMENT '同uid',
  `uuid` varchar(100) NOT NULL COMMENT 'uuid',
  `thirdBind` tinyint(4) NOT NULL COMMENT '第三方绑定类型1-qq 2-wx',
  `thirdId` varchar(60) NOT NULL DEFAULT '' COMMENT '第三方id wx_ios_xxxxx',
  `platform` varchar(10) NOT NULL COMMENT '平台 ios 、android',
  `secretKey` varchar(32) NOT NULL DEFAULT '' COMMENT '密钥',
  `zone` varchar(20) NOT NULL COMMENT '分配的zone',
  `channel` varchar(30) NOT NULL DEFAULT 'unknown' COMMENT '注册渠道',
  `deviceId` varchar(100) NOT NULL DEFAULT '',
  `registerAppVersion` varchar(50) NOT NULL DEFAULT '100' COMMENT '注册版本号',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `uuid` (`uuid`),
  KEY `thirdId` (`thirdId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "thirdId"';



# Dump of table center_user_test
# ------------------------------------------------------------

DROP TABLE IF EXISTS `center_user_test`;

CREATE TABLE `center_user_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否被删除：0，否；1，是。',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customerId` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table reward_send_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reward_send_log`;

CREATE TABLE `reward_send_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reward_name` varchar(256) NOT NULL,
  `reward_items` varchar(256) NOT NULL,
  `users` text NOT NULL,
  `operator` varchar(20) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table rule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rule`;

CREATE TABLE `rule` (
  `ruleId` int(11) NOT NULL AUTO_INCREMENT,
  `ruleName` varchar(256) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1-on 2-off 3-archived',
  `type` tinyint(4) NOT NULL COMMENT '1-basic 2-Experiment',
  `scheduleStart` int(11) NOT NULL COMMENT '开始时间（为0不限制）',
  `scheduleEnd` int(11) NOT NULL COMMENT '结束时间（为0不限制）',
  `description` varchar(1024) NOT NULL,
  `parentRule` int(11) NOT NULL COMMENT '父规则  0-顶级规则',
  `isParent` tinyint(4) NOT NULL COMMENT '该类目是否为父类目(即：该类目是否还有子类目) 1-是 0-不是',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`ruleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='规则';



# Dump of table rule_attribute
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rule_attribute`;

CREATE TABLE `rule_attribute` (
  `aid` int(11) NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attr_name` varchar(100) NOT NULL COMMENT '属性名',
  `choices` varchar(100) NOT NULL COMMENT '=,>,<,!=,>=,<=',
  `attrType` int(11) NOT NULL COMMENT '1-数字 2-日期 3-string 4-单选 5-多选',
  `groupId` int(11) NOT NULL,
  `groupName` varchar(100) NOT NULL,
  `value` text COMMENT '默认值',
  `regular` varchar(100) DEFAULT NULL COMMENT '校验规则',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='规则属性';



# Dump of table rule_configurations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rule_configurations`;

CREATE TABLE `rule_configurations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ruleId` int(11) NOT NULL COMMENT 'ruleId',
  `type` tinyint(4) NOT NULL COMMENT '类型 1-json 2-filePath',
  `value` text NOT NULL COMMENT '值',
  `md5` varchar(32) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ruleId` (`ruleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='规则配置';



# Dump of table rule_criteria
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rule_criteria`;

CREATE TABLE `rule_criteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ruleId` int(11) NOT NULL,
  `aid` int(11) NOT NULL COMMENT '属性id',
  `choices` varchar(100) NOT NULL COMMENT '=,>,<,!=,>=,<=',
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table server
# ------------------------------------------------------------

DROP TABLE IF EXISTS `server`;

CREATE TABLE `server` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `server_id` varchar(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:激活，0:关闭',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '1:gameSvr,2:globalSvr',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table tb_fnxnbb_onlinecnt
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tb_fnxnbb_onlinecnt`;

CREATE TABLE `tb_fnxnbb_onlinecnt` (
  `gameappid` varchar(32) NOT NULL DEFAULT '',
  `timekey` int(11) NOT NULL DEFAULT '0',
  `reporttime` int(11) NOT NULL DEFAULT '0' COMMENT '上报服务器时间戳',
  `gsid` varchar(32) NOT NULL DEFAULT '' COMMENT '服务器编号',
  `zoneareaid` int(11) NOT NULL DEFAULT '0' COMMENT '分区分服ID',
  `onlinecntios` int(11) NOT NULL DEFAULT '0' COMMENT 'ios在线人数',
  `onlinecntandroid` int(11) NOT NULL DEFAULT '0' COMMENT 'android在线人数',
  KEY `timekey` (`timekey`,`gameappid`,`gsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='shard_key "timekey"';




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
