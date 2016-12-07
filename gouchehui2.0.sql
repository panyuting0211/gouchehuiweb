/*
Navicat MySQL Data Transfer

Source Server         : www.gouchehui.com
Source Server Version : 50520
Source Host           : www.gouchehui.com:3306
Source Database       : gouchehui2.0

Target Server Type    : MYSQL
Target Server Version : 50520
File Encoding         : 65001

Date: 2016-12-06 14:20:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gch_access
-- ----------------------------
DROP TABLE IF EXISTS `gch_access`;
CREATE TABLE `gch_access` (
  `role_id` smallint(6) unsigned NOT NULL COMMENT '角色id',
  `node_id` smallint(6) unsigned NOT NULL COMMENT '节点id',
  `level` tinyint(1) NOT NULL COMMENT '级别',
  `module` varchar(50) DEFAULT NULL COMMENT '模块',
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='rbac';

-- ----------------------------
-- Table structure for gch_actives_log
-- ----------------------------
DROP TABLE IF EXISTS `gch_actives_log`;
CREATE TABLE `gch_actives_log` (
  `id` varchar(32) NOT NULL COMMENT '主键ID',
  `cpid` varchar(32) NOT NULL COMMENT '报价ID',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户ID',
  `click_num` int(7) unsigned DEFAULT '0' COMMENT '点击次数',
  `create_time` varchar(15) DEFAULT NULL COMMENT '点击时间',
  `update_time` varchar(15) DEFAULT NULL COMMENT '最新一次点击的时间',
  `ip` varchar(25) DEFAULT NULL COMMENT 'IP',
  `ip_click` int(7) unsigned DEFAULT '0' COMMENT '同一个IP点击的次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_activities
-- ----------------------------
DROP TABLE IF EXISTS `gch_activities`;
CREATE TABLE `gch_activities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_name` varchar(50) DEFAULT NULL COMMENT '活动名称',
  `activity_number` varchar(50) DEFAULT NULL COMMENT '活动标注',
  `starttime` int(11) NOT NULL COMMENT '开始时间',
  `endtime` int(11) NOT NULL COMMENT '结束时间',
  `status` int(1) DEFAULT '1' COMMENT '状态(1：未开启,2:已开启)',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createuser` varchar(32) DEFAULT NULL,
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updateuser` varchar(32) DEFAULT NULL,
  `isdelete` smallint(6) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_ad
-- ----------------------------
DROP TABLE IF EXISTS `gch_ad`;
CREATE TABLE `gch_ad` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `ad_name` varchar(200) DEFAULT NULL COMMENT '标题',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片地址',
  `type` int(11) DEFAULT NULL COMMENT '类型 0、网站 1、app 2、其他',
  `status` smallint(1) NOT NULL COMMENT '状态(1显示0不显示)',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `adurl` varchar(350) DEFAULT NULL,
  `alif` varchar(6) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告表';

-- ----------------------------
-- Table structure for gch_app_push
-- ----------------------------
DROP TABLE IF EXISTS `gch_app_push`;
CREATE TABLE `gch_app_push` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `mess_type` varchar(100) DEFAULT NULL COMMENT '消息类型',
  `mess_id` varchar(32) DEFAULT NULL COMMENT '对应id',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `message` varchar(200) DEFAULT NULL COMMENT '内容',
  `icon` varchar(350) DEFAULT NULL COMMENT '图标',
  `from_user` varchar(32) DEFAULT NULL COMMENT '发送人',
  `to_user` varchar(32) DEFAULT NULL COMMENT '接收人',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_app_version
-- ----------------------------
DROP TABLE IF EXISTS `gch_app_version`;
CREATE TABLE `gch_app_version` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `version_no` varchar(32) DEFAULT NULL COMMENT '版本号',
  `type` int(11) DEFAULT '0' COMMENT '平台0、安卓平台 1、IOS平台 2、其他',
  `app_url` varchar(150) DEFAULT NULL COMMENT 'app地址',
  `version_content` varchar(3000) DEFAULT NULL COMMENT '版本更新介绍',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `app_size` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_area
-- ----------------------------
DROP TABLE IF EXISTS `gch_area`;
CREATE TABLE `gch_area` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `area_name` varchar(20) NOT NULL COMMENT '区域名称',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='4S所在区域';

-- ----------------------------
-- Table structure for gch_attention_push
-- ----------------------------
DROP TABLE IF EXISTS `gch_attention_push`;
CREATE TABLE `gch_attention_push` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_model_id` varchar(32) DEFAULT NULL COMMENT '所属车型',
  `content` varchar(3000) DEFAULT NULL COMMENT '推送内容(HTML源码)',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关注车型推送消息表';

-- ----------------------------
-- Table structure for gch_big_brand
-- ----------------------------
DROP TABLE IF EXISTS `gch_big_brand`;
CREATE TABLE `gch_big_brand` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `big_brand_name` varchar(50) DEFAULT NULL COMMENT '大品牌名称',
  `create_time` varchar(15) DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(30) DEFAULT NULL COMMENT '创建人',
  `update_time` varchar(15) DEFAULT NULL COMMENT '更新时间',
  `update_user` varchar(30) DEFAULT NULL COMMENT '更新用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_brand
-- ----------------------------
DROP TABLE IF EXISTS `gch_brand`;
CREATE TABLE `gch_brand` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `brand_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `logo` varchar(240) DEFAULT NULL COMMENT 'logo',
  `access_quantity` int(11) DEFAULT '0' COMMENT '访问量',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `alif` varchar(2) DEFAULT NULL,
  `fid` varchar(32) DEFAULT NULL COMMENT '大品牌ID',
  PRIMARY KEY (`id`),
  KEY `alif` (`alif`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='品牌表';

-- ----------------------------
-- Table structure for gch_brand_manager
-- ----------------------------
DROP TABLE IF EXISTS `gch_brand_manager`;
CREATE TABLE `gch_brand_manager` (
  `id` varchar(32) NOT NULL COMMENT 'id',
  `manager` varchar(32) DEFAULT NULL COMMENT '管理者',
  `tel` varchar(12) DEFAULT NULL COMMENT '联系方式',
  `brand_id` varchar(32) DEFAULT NULL COMMENT '品牌id',
  `brand_name` varchar(32) DEFAULT NULL COMMENT '品牌名字',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) NOT NULL COMMENT '创建人',
  `isdelete` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) NOT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COMMENT='积分详情表';

-- ----------------------------
-- Table structure for gch_buy_car_package
-- ----------------------------
DROP TABLE IF EXISTS `gch_buy_car_package`;
CREATE TABLE `gch_buy_car_package` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `brand_id` varchar(32) DEFAULT NULL COMMENT '所属品牌ID',
  `min_price` double DEFAULT NULL COMMENT '最低价',
  `max_price` double DEFAULT NULL COMMENT '最高价',
  `price` double DEFAULT NULL COMMENT '礼包价格',
  `imgurl` varchar(150) DEFAULT NULL COMMENT '图片',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `description` varchar(320) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='特价车礼包表';

-- ----------------------------
-- Table structure for gch_car
-- ----------------------------
DROP TABLE IF EXISTS `gch_car`;
CREATE TABLE `gch_car` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_model_id` varchar(32) DEFAULT NULL COMMENT '所属车型',
  `access_quantity` int(11) DEFAULT '0' COMMENT '访问量',
  `car_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `alif` varchar(2) DEFAULT NULL,
  `xj_count` int(11) DEFAULT '0' COMMENT '询价总数',
  `auth_price` int(9) DEFAULT NULL COMMENT '官方指导价',
  `order` mediumint(9) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '车款状态 1：在售；0：停售。',
  `displacement` varchar(20) DEFAULT NULL COMMENT '排量',
  PRIMARY KEY (`id`),
  KEY `car_model_id` (`car_model_id`) USING HASH,
  KEY `status` (`status`) USING BTREE,
  KEY `xj_count` (`xj_count`) USING BTREE,
  KEY `car_id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车款表';

-- ----------------------------
-- Table structure for gch_car_activities
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_activities`;
CREATE TABLE `gch_car_activities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `activity_id` int(11) DEFAULT NULL COMMENT '活动ID',
  `activity_number` varchar(50) DEFAULT NULL COMMENT '活动编号',
  `car_id` varchar(32) DEFAULT '' COMMENT '车款id',
  `car_price_id` varchar(32) DEFAULT NULL COMMENT '报价ID',
  `type` int(1) DEFAULT '0' COMMENT '1:立即购买2：马上抢，3：直降，4：打折',
  `buyer_count` int(32) DEFAULT NULL COMMENT '购买者数量',
  `status` int(8) DEFAULT '1' COMMENT '状态（1：关闭，2：开启）',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createuser` varchar(32) DEFAULT NULL,
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updateuser` varchar(32) DEFAULT NULL,
  `isdelete` int(6) DEFAULT '0',
  `sort` int(5) DEFAULT NULL COMMENT '排序',
  `group_id` varchar(32) DEFAULT NULL COMMENT '分组ID',
  `group_name` varchar(30) DEFAULT NULL COMMENT '组名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=364 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_color_image
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_color_image`;
CREATE TABLE `gch_car_color_image` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `type` int(11) DEFAULT NULL COMMENT '所属分类 0外观 ，1内饰，2空间，3细节',
  `car_id` varchar(32) NOT NULL COMMENT '查款id',
  `exterior_color_id` varchar(32) DEFAULT NULL COMMENT '外观颜色ID',
  `interior_color_id` varchar(32) NOT NULL COMMENT '内饰颜色ID',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`),
  KEY `car_id` (`car_id`) USING HASH,
  KEY `exterior_color_id` (`exterior_color_id`) USING HASH,
  KEY `interior_color_id` (`interior_color_id`) USING HASH,
  KEY `type` (`type`) USING HASH,
  KEY `isdelete` (`isdelete`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车款-颜色图库表';

-- ----------------------------
-- Table structure for gch_car_exterior_color_image
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_exterior_color_image`;
CREATE TABLE `gch_car_exterior_color_image` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `exterior_color_id` varchar(32) DEFAULT NULL COMMENT '外观颜色id',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `car_id` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `car_id` (`car_id`) USING HASH,
  KEY `exterior_color_id` (`exterior_color_id`) USING BTREE,
  KEY `imgurl` (`imgurl`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_friend_act
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_friend_act`;
CREATE TABLE `gch_car_friend_act` (
  `id` varchar(32) NOT NULL,
  `title` varchar(100) DEFAULT NULL COMMENT '活动标题',
  `club_id` varchar(32) DEFAULT NULL COMMENT '发起方(俱乐部)',
  `introduce` varchar(200) DEFAULT NULL COMMENT '活动简介',
  `contents` text COMMENT '活动内容',
  `start_time` varchar(15) DEFAULT NULL COMMENT '活动开始时间',
  `end_time` varchar(15) DEFAULT NULL COMMENT '活动结束时间',
  `counts` int(11) DEFAULT NULL COMMENT '活动被浏览次数',
  `act_img` varchar(55) DEFAULT NULL COMMENT '活动图标',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_friend_nan
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_friend_nan`;
CREATE TABLE `gch_car_friend_nan` (
  `id` varchar(32) NOT NULL,
  `club_name` varchar(30) DEFAULT NULL COMMENT '俱乐部名称',
  `user_id` varchar(32) DEFAULT NULL COMMENT '创建人ID',
  `club_logo` varchar(255) DEFAULT NULL COMMENT '俱乐部LOGO',
  `introduce` text COMMENT '俱乐部简介',
  `user_num` int(10) unsigned DEFAULT '0' COMMENT '成员数量',
  `president` varchar(30) DEFAULT NULL COMMENT '会长',
  `act_num` int(10) unsigned zerofill DEFAULT '0000000000' COMMENT '活动数量',
  `dynamic_num` int(10) unsigned zerofill DEFAULT '0000000000' COMMENT '动态数量',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_life
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_life`;
CREATE TABLE `gch_car_life` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `type_name` varchar(15) DEFAULT NULL COMMENT '分类名称',
  `level` varchar(2) DEFAULT NULL COMMENT '分类级别(1,2)',
  `pid` varchar(32) DEFAULT NULL COMMENT '父id',
  `sort` smallint(2) DEFAULT '0' COMMENT '分类排序',
  `controller_name` varchar(32) DEFAULT NULL COMMENT '方法控制器名称',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for gch_car_life_bbs
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_life_bbs`;
CREATE TABLE `gch_car_life_bbs` (
  `id` varchar(32) NOT NULL,
  `type_id` varchar(32) DEFAULT NULL COMMENT '帖子分类ID',
  `user_id` varchar(32) DEFAULT NULL COMMENT '发帖人ID',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `summary` text COMMENT '摘要',
  `contents` longtext COMMENT '内容',
  `image` varchar(255) DEFAULT NULL COMMENT '帖子图像',
  `club_id` varchar(32) DEFAULT NULL COMMENT '俱乐部ID',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款id',
  `final_price` int(11) DEFAULT NULL COMMENT '落地价',
  `car_own` varchar(32) DEFAULT NULL COMMENT '车主',
  `click_amount` int(11) DEFAULT '0' COMMENT '点击数',
  `comment_amount` int(8) DEFAULT '0' COMMENT '评论数',
  `thumbs_amount` int(8) DEFAULT '0' COMMENT '点赞数',
  `display` smallint(1) DEFAULT '1' COMMENT '是否显示（1：不显示，2：显示）',
  `check` smallint(1) DEFAULT '1' COMMENT '是否审核（1：未审核，2：审核通过，3：审核失败）',
  `check_fail` varchar(255) DEFAULT NULL COMMENT '审核失败的原因',
  `place_hot` smallint(1) DEFAULT '1' COMMENT '热门推荐放置（1：未放置，2：放置）',
  `place_index` smallint(1) DEFAULT '1' COMMENT '帖子首页放置（1：未放置，2：放置）',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='\r\n';

-- ----------------------------
-- Table structure for gch_car_life_club
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_life_club`;
CREATE TABLE `gch_car_life_club` (
  `id` varchar(32) NOT NULL,
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户ID',
  `club_id` varchar(32) DEFAULT NULL COMMENT '俱乐部ID',
  `create_time` varchar(15) DEFAULT NULL COMMENT '加入的时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_life_comment
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_life_comment`;
CREATE TABLE `gch_car_life_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户id',
  `comment` text COMMENT '品论内容',
  `pid` int(11) DEFAULT '0' COMMENT '上一级回复的id',
  `rid` int(11) DEFAULT '0' COMMENT '根评论的id',
  `bbs_id` varchar(32) DEFAULT NULL COMMENT '帖子id',
  `thumbs_amount` int(12) DEFAULT '0' COMMENT '评论id',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_life_thumbs
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_life_thumbs`;
CREATE TABLE `gch_car_life_thumbs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户id',
  `bbs_id` varchar(32) DEFAULT NULL COMMENT '帖子id',
  `comment_id` int(11) DEFAULT NULL COMMENT '评论id',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_love
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_love`;
CREATE TABLE `gch_car_love` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户ID',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款ID',
  `from` tinyint(2) DEFAULT '1' COMMENT '车款来源（1：车库车款表，2：会员中心车款表）',
  `buy_time` date DEFAULT NULL COMMENT '购买时间',
  `province_id` varchar(32) DEFAULT NULL COMMENT '购车省份',
  `city_id` varchar(32) DEFAULT NULL COMMENT '购车城市',
  `image` varchar(255) DEFAULT NULL COMMENT '爱车图片',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` smallint(2) unsigned zerofill DEFAULT '00' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_model
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_model`;
CREATE TABLE `gch_car_model` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `brand_id` varchar(32) DEFAULT NULL COMMENT '所属品牌',
  `car_type_id` varchar(32) DEFAULT NULL COMMENT '所属分类',
  `access_quantity` int(11) unsigned DEFAULT '0' COMMENT '访问量',
  `car_model_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `alif` varchar(2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '车型状态，1：在售；0：停售',
  `xj_count` int(11) DEFAULT '0' COMMENT '询价次数',
  `attention_count` int(11) DEFAULT '0' COMMENT '关注度',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING HASH,
  KEY `car_type_id` (`car_type_id`) USING HASH,
  KEY `access_quantity` (`access_quantity`) USING HASH,
  KEY `isdelete` (`isdelete`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车型表';

-- ----------------------------
-- Table structure for gch_car_model_prefer
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_model_prefer`;
CREATE TABLE `gch_car_model_prefer` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `brand_id` varchar(32) NOT NULL COMMENT '所属品牌',
  `car_model_id` varchar(32) NOT NULL COMMENT '车型ID',
  `user_id` varchar(32) NOT NULL COMMENT '访问量',
  `car_model_name` varchar(100) NOT NULL COMMENT '名称',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`) USING HASH,
  KEY `access_quantity` (`user_id`) USING HASH,
  KEY `isdelete` (`isdelete`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='4s用户车型喜好表';

-- ----------------------------
-- Table structure for gch_car_plan
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_plan`;
CREATE TABLE `gch_car_plan` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户ID',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款ID',
  `buy_time` varchar(32) DEFAULT NULL COMMENT '购买时间',
  `province_id` varchar(32) DEFAULT NULL COMMENT '购车省份',
  `city_id` varchar(32) DEFAULT NULL COMMENT '购车城市',
  `from_plan` varchar(100) DEFAULT NULL COMMENT '入口页面',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` smallint(2) unsigned zerofill DEFAULT '00' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_car_prefer
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_prefer`;
CREATE TABLE `gch_car_prefer` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '访问量',
  `car_model_id` varchar(32) NOT NULL COMMENT '所属车型',
  `car_name` varchar(100) NOT NULL COMMENT '名称',
  `user_id` varchar(32) NOT NULL COMMENT '描述',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) NOT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`),
  KEY `car_model_id` (`car_model_id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车款表';

-- ----------------------------
-- Table structure for gch_car_price
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_price`;
CREATE TABLE `gch_car_price` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `exterior_color_id` varchar(32) DEFAULT NULL COMMENT '外观颜色ID',
  `interior_color_id` varchar(32) NOT NULL COMMENT '内饰颜色ID',
  `user_id` varchar(32) DEFAULT NULL COMMENT '所属用户',
  `car_id` varchar(32) NOT NULL,
  `is_xunjia` smallint(6) NOT NULL DEFAULT '1' COMMENT '是否需要询价(1需要询价,2不需要询价)',
  `access_quantity` int(11) DEFAULT '0' COMMENT '访问量',
  `transactions_count` int(11) DEFAULT '0' COMMENT '成交量',
  `asking_price_count` int(11) DEFAULT '0' COMMENT '询价量',
  `price` int(10) DEFAULT NULL COMMENT '指导价',
  `discount` int(11) DEFAULT NULL COMMENT '优惠',
  `stock` varchar(10) DEFAULT NULL COMMENT '库存',
  `onway` varchar(10) DEFAULT NULL COMMENT '在途',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `createip` varchar(32) DEFAULT NULL COMMENT '创建者的ip',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `updateip` varchar(32) DEFAULT NULL COMMENT '修改者的ip',
  `low_price` int(10) DEFAULT NULL COMMENT '报价',
  `show_index` int(11) NOT NULL AUTO_INCREMENT COMMENT '显示索引',
  `month_status` int(11) DEFAULT '0' COMMENT '月份（底价次数标志）',
  `quarter_status` int(11) DEFAULT '0' COMMENT '季度（底价次数标志）',
  `status_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '成为底价车的时间',
  PRIMARY KEY (`id`),
  KEY `low_price` (`low_price`),
  KEY `exterior_color_id` (`exterior_color_id`) USING BTREE,
  KEY `interior_color_id` (`interior_color_id`) USING BTREE,
  KEY `car_id` (`car_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `show_index` (`show_index`) USING BTREE,
  KEY `is_xunjia` (`is_xunjia`) USING BTREE,
  KEY `isdelete` (`isdelete`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33987 DEFAULT CHARSET=utf8 COMMENT='报价表';

-- ----------------------------
-- Table structure for gch_car_question
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_question`;
CREATE TABLE `gch_car_question` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '主键',
  `car_brand_id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '汽车品牌ID',
  `car_model_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '所属车型',
  `question` varchar(2000) COLLATE utf8_bin DEFAULT NULL COMMENT '问题',
  `from` int(1) DEFAULT '1' COMMENT '来源(1:PCweb，2：微信端，3：移动web)',
  `question_type` smallint(1) unsigned NOT NULL COMMENT '问题分类（1.商品资讯2.支付相关3.发票问题4.其它问题）',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '创建人(提问人)',
  `status` smallint(1) unsigned DEFAULT '2' COMMENT '问题状态(1.显示2.不显示)',
  `reply` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '回复的内容',
  `reply_time` timestamp NULL DEFAULT NULL COMMENT '回复时间',
  `reply_status` smallint(1) unsigned DEFAULT '0' COMMENT '回复状态（0：未回复，1：已回复）',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '修改人',
  `isdelete` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='提问表';

-- ----------------------------
-- Table structure for gch_car_question_result
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_question_result`;
CREATE TABLE `gch_car_question_result` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_question_id` varchar(32) DEFAULT NULL COMMENT '所属提问',
  `to_user` varchar(32) DEFAULT NULL COMMENT '接收人',
  `from_user` varchar(32) DEFAULT NULL COMMENT '回复人',
  `content` varchar(2000) DEFAULT NULL COMMENT '回复内容',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问题回复表';

-- ----------------------------
-- Table structure for gch_car_type
-- ----------------------------
DROP TABLE IF EXISTS `gch_car_type`;
CREATE TABLE `gch_car_type` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_type_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `logo` varchar(240) DEFAULT NULL COMMENT 'logo',
  `access_quantity` int(11) DEFAULT '0' COMMENT '访问量',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车类型（SUV、MPV等）';

-- ----------------------------
-- Table structure for gch_city
-- ----------------------------
DROP TABLE IF EXISTS `gch_city`;
CREATE TABLE `gch_city` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `province_id` varchar(32) DEFAULT NULL COMMENT '所属省份',
  `city_name` varchar(20) DEFAULT NULL COMMENT '城市名称',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `status` tinyint(11) NOT NULL DEFAULT '1' COMMENT '是否启用 1启用 0不启用',
  PRIMARY KEY (`id`),
  KEY `isdelete` (`isdelete`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='4S店所在城市';

-- ----------------------------
-- Table structure for gch_coin
-- ----------------------------
DROP TABLE IF EXISTS `gch_coin`;
CREATE TABLE `gch_coin` (
  `id` varchar(32) NOT NULL COMMENT 'id',
  `user_id` varchar(32) NOT NULL COMMENT '关联user的id',
  `role` tinyint(4) DEFAULT NULL COMMENT '角色',
  `coin` int(6) NOT NULL COMMENT '车币',
  `info` varchar(255) NOT NULL COMMENT '积分增减说明',
  `flag` int(8) DEFAULT '0' COMMENT '来源标志',
  `from_id` varchar(32) DEFAULT '' COMMENT '来源ID',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) NOT NULL COMMENT '创建人',
  `isdelete` int(11) NOT NULL COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) NOT NULL COMMENT '修改人',
  `types` varchar(20) DEFAULT NULL COMMENT '区分类别',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COMMENT='积分详情表';

-- ----------------------------
-- Table structure for gch_color
-- ----------------------------
DROP TABLE IF EXISTS `gch_color`;
CREATE TABLE `gch_color` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `color_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `color_value` varchar(100) DEFAULT NULL COMMENT '色值',
  `type` int(11) DEFAULT NULL COMMENT '分类1、汽车外观 2.、内饰',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='所有颜色表';

-- ----------------------------
-- Table structure for gch_data_city
-- ----------------------------
DROP TABLE IF EXISTS `gch_data_city`;
CREATE TABLE `gch_data_city` (
  `CityID` bigint(20) NOT NULL,
  `CityName` varchar(50) DEFAULT NULL,
  `ZipCode` varchar(50) DEFAULT NULL,
  `ProvinceID` bigint(20) DEFAULT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `DateUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`CityID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_data_district
-- ----------------------------
DROP TABLE IF EXISTS `gch_data_district`;
CREATE TABLE `gch_data_district` (
  `DistrictID` bigint(20) NOT NULL,
  `DistrictName` varchar(50) DEFAULT NULL,
  `CityID` bigint(20) DEFAULT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `DateUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`DistrictID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_data_province
-- ----------------------------
DROP TABLE IF EXISTS `gch_data_province`;
CREATE TABLE `gch_data_province` (
  `ProvinceID` bigint(20) NOT NULL,
  `ProvinceName` varchar(50) DEFAULT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `DateUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`ProvinceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_exterior_color
-- ----------------------------
DROP TABLE IF EXISTS `gch_exterior_color`;
CREATE TABLE `gch_exterior_color` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_id` varchar(32) DEFAULT NULL COMMENT '所属车款',
  `access_quantity` int(11) DEFAULT '0' COMMENT '访问量',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `color_id` varchar(32) DEFAULT NULL COMMENT '外观颜色ID',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车款外观颜色';

-- ----------------------------
-- Table structure for gch_exterior_color_prefer
-- ----------------------------
DROP TABLE IF EXISTS `gch_exterior_color_prefer`;
CREATE TABLE `gch_exterior_color_prefer` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户id',
  `car_model_id` varchar(32) DEFAULT NULL COMMENT '车型ID',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款ID',
  `exterior_color_id` varchar(32) DEFAULT NULL COMMENT '车款外观颜色ID',
  `exterior_color_name` varchar(40) DEFAULT NULL COMMENT '外观颜色名称',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`),
  KEY `car_id` (`exterior_color_id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车款外观颜色';

-- ----------------------------
-- Table structure for gch_hot_brand
-- ----------------------------
DROP TABLE IF EXISTS `gch_hot_brand`;
CREATE TABLE `gch_hot_brand` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `client` int(4) unsigned NOT NULL COMMENT '客户端分类(1：手机客户端，2：app,3:PC）',
  `brand_id` varchar(32) NOT NULL COMMENT '品牌ID',
  `order` int(1) unsigned DEFAULT NULL COMMENT '排序',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(50) DEFAULT NULL COMMENT '创建人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_image
-- ----------------------------
DROP TABLE IF EXISTS `gch_image`;
CREATE TABLE `gch_image` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` int(11) DEFAULT NULL COMMENT '所属分类 0外观 ，1内饰，2空间，3细节',
  `car_id` varchar(32) NOT NULL COMMENT '查款id',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61616 DEFAULT CHARSET=utf8 COMMENT='车款-颜色图库表';

-- ----------------------------
-- Table structure for gch_index_car_price
-- ----------------------------
DROP TABLE IF EXISTS `gch_index_car_price`;
CREATE TABLE `gch_index_car_price` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_id` varchar(32) DEFAULT NULL COMMENT '所属车款',
  `client` int(4) unsigned DEFAULT '1' COMMENT '客户端(1:pc,2:手机web端)',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '状态(1显示2不显示)',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `exterior_color_id` varchar(32) DEFAULT NULL COMMENT '外观颜色ID',
  `interior_color_id` varchar(32) DEFAULT NULL COMMENT '内饰颜色ID',
  `area_name` varchar(255) DEFAULT NULL COMMENT '所在名称',
  `brand_alif` varchar(12) NOT NULL COMMENT '品牌排序',
  `brand_id` varchar(32) NOT NULL COMMENT '品牌id',
  `brand_name` varchar(50) NOT NULL COMMENT '品牌名称',
  `model_name` varchar(32) NOT NULL COMMENT '车型名称',
  `model_id` varchar(32) NOT NULL COMMENT '车型id',
  `isbaojia` int(1) NOT NULL COMMENT '是否有报价 1有 0没有',
  PRIMARY KEY (`id`),
  KEY `carid` (`car_id`) USING BTREE,
  KEY `baojia` (`isbaojia`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_interaction
-- ----------------------------
DROP TABLE IF EXISTS `gch_interaction`;
CREATE TABLE `gch_interaction` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `context` varchar(2000) DEFAULT NULL COMMENT '内容',
  `address` varchar(100) DEFAULT NULL COMMENT '位置',
  `sta` int(11) DEFAULT '0' COMMENT '可见状态0、可见 1、不可见',
  `user_id` varchar(32) DEFAULT NULL COMMENT '发布人',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='互动提问表';

-- ----------------------------
-- Table structure for gch_interaction_image
-- ----------------------------
DROP TABLE IF EXISTS `gch_interaction_image`;
CREATE TABLE `gch_interaction_image` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `interaction_id` varchar(32) DEFAULT NULL COMMENT '所属互动',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='互动图库';

-- ----------------------------
-- Table structure for gch_interaction_result
-- ----------------------------
DROP TABLE IF EXISTS `gch_interaction_result`;
CREATE TABLE `gch_interaction_result` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `interaction_id` varchar(32) DEFAULT NULL COMMENT '所属互动',
  `to_user` varchar(32) DEFAULT NULL COMMENT '接收人',
  `from_user` varchar(32) DEFAULT NULL COMMENT '回复人',
  `content` varchar(2000) DEFAULT NULL COMMENT '回复内容',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='互动回复表';

-- ----------------------------
-- Table structure for gch_interior_color
-- ----------------------------
DROP TABLE IF EXISTS `gch_interior_color`;
CREATE TABLE `gch_interior_color` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_id` varchar(32) DEFAULT NULL COMMENT '所属车款',
  `access_quantity` int(11) DEFAULT '0' COMMENT '访问量',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `color_id` varchar(32) DEFAULT NULL COMMENT '内饰颜色ID',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`),
  KEY `car_id` (`car_id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车款内饰颜色';

-- ----------------------------
-- Table structure for gch_interior_color_prefer
-- ----------------------------
DROP TABLE IF EXISTS `gch_interior_color_prefer`;
CREATE TABLE `gch_interior_color_prefer` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户id',
  `car_model_id` varchar(32) DEFAULT NULL COMMENT '车型ID',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款ID',
  `interior_color_id` varchar(32) DEFAULT NULL COMMENT '车款内饰颜色ID',
  `interior_color_name` varchar(50) DEFAULT NULL COMMENT '内饰颜色名称',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`),
  KEY `car_id` (`interior_color_id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车款内饰颜色';

-- ----------------------------
-- Table structure for gch_manager
-- ----------------------------
DROP TABLE IF EXISTS `gch_manager`;
CREATE TABLE `gch_manager` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '后台管理员id',
  `admin_name` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '管理员用户名',
  `pwd` varchar(64) NOT NULL COMMENT '管理员密码',
  `email` varchar(50) NOT NULL COMMENT '用户邮箱',
  `status` smallint(6) NOT NULL COMMENT '管理员状态(1:开启，0：禁用)',
  `last_login_time` int(11) NOT NULL COMMENT '上次登录时间',
  `last_login_ip` varchar(40) NOT NULL COMMENT '上次登录IP',
  `createtime` int(11) NOT NULL,
  `name` varchar(16) NOT NULL COMMENT '姓名',
  `tel` varchar(16) DEFAULT NULL COMMENT '手机号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台管理员';

-- ----------------------------
-- Table structure for gch_member_car
-- ----------------------------
DROP TABLE IF EXISTS `gch_member_car`;
CREATE TABLE `gch_member_car` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `car_model_id` varchar(32) DEFAULT NULL COMMENT '所属车型',
  `car_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `imgurl` varchar(240) DEFAULT NULL COMMENT '图片',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`),
  KEY `car_model_id` (`car_model_id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='车款表';

-- ----------------------------
-- Table structure for gch_node
-- ----------------------------
DROP TABLE IF EXISTS `gch_node`;
CREATE TABLE `gch_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` varchar(6) DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  `display` smallint(1) DEFAULT NULL COMMENT '是否显示(1:显示，0:不显示)',
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=253 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_online_feedback
-- ----------------------------
DROP TABLE IF EXISTS `gch_online_feedback`;
CREATE TABLE `gch_online_feedback` (
  `id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '在线反馈ID',
  `username` varchar(15) CHARACTER SET utf8 NOT NULL COMMENT '反馈者姓名',
  `type` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '问题类型',
  `tel` varchar(11) COLLATE utf8_bin DEFAULT NULL COMMENT '手机号',
  `email` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '反馈者邮箱',
  `view` varchar(200) CHARACTER SET utf8 NOT NULL COMMENT '反馈意见',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='反馈表';

-- ----------------------------
-- Table structure for gch_pay
-- ----------------------------
DROP TABLE IF EXISTS `gch_pay`;
CREATE TABLE `gch_pay` (
  `id` varchar(32) NOT NULL COMMENT '订单ID',
  `out_trade_no` varchar(100) DEFAULT NULL COMMENT '订单号',
  `user_id` varchar(32) NOT NULL COMMENT '购车人平台账号',
  `carstyle` varchar(100) DEFAULT NULL COMMENT '品牌+车型(例 奥迪A4L）',
  `brand_id` varchar(32) DEFAULT '' COMMENT '品牌ID',
  `brand_name` varchar(32) DEFAULT '' COMMENT '品牌名称',
  `car_model_id` varchar(32) DEFAULT '' COMMENT '车型ID',
  `car_model_name` varchar(32) DEFAULT '' COMMENT '车型名称',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款ID',
  `car_name` varchar(255) DEFAULT NULL COMMENT '车款名称',
  `exterior_color_id` varchar(32) DEFAULT NULL COMMENT '外观颜色ID',
  `exterior_color_name` varchar(30) DEFAULT NULL COMMENT '汽车颜色名字',
  `exterior_color_value` varchar(20) DEFAULT NULL COMMENT '外观颜色值',
  `interior_color_id` varchar(32) DEFAULT NULL COMMENT '内饰颜色ID',
  `interior_color_name` varchar(30) DEFAULT NULL COMMENT '内饰颜色名字',
  `interior_color_value` varchar(20) DEFAULT NULL COMMENT '内饰颜色值',
  `exterior_img` varchar(255) DEFAULT NULL COMMENT '外观颜色图片',
  `money` decimal(10,2) DEFAULT NULL COMMENT '支付金额',
  `order_money` int(255) DEFAULT NULL COMMENT '原始订单价格',
  `ticket_money` int(10) DEFAULT NULL COMMENT '优惠券金额',
  `status` int(8) unsigned DEFAULT '0' COMMENT '0未支付 ，1已支付（未完善），2已失效，3审核中,4通过审核，5未通过审核，6出库中，7提车中，8已完成，9支付中，10支付失败，11支付取消',
  `status_track` int(1) DEFAULT '1' COMMENT '询价订单状态（1：未跟踪，2：已跟踪）',
  `pay_obj` tinyint(1) unsigned DEFAULT NULL COMMENT '支付对象(1:询价2:订金,3:一键询价,4:一元抢（双十一）)',
  `type` int(11) DEFAULT '1' COMMENT '车辆类型 1、底价车 2、特价车',
  `buy_way` varchar(15) DEFAULT NULL COMMENT '付款方式',
  `buyer_name` varchar(20) DEFAULT '' COMMENT '购买人姓名',
  `buyer_tel` varchar(11) DEFAULT '' COMMENT '支付人手机',
  `buy_time` varchar(30) DEFAULT NULL COMMENT '购车时间',
  `car_mode` varchar(15) DEFAULT '自提' COMMENT '提车方式',
  `pay_way` varchar(35) DEFAULT NULL COMMENT '支付方式(支付宝,微信支付等)',
  `pay_ip` varchar(15) DEFAULT NULL COMMENT '支付人ip',
  `card_place` varchar(50) DEFAULT NULL COMMENT '上牌地点',
  `pay_err_code_des` varchar(255) DEFAULT NULL COMMENT '支付失败返回描述',
  `sign` varchar(32) DEFAULT NULL COMMENT '微信支付签名',
  `callback` varchar(255) NOT NULL COMMENT '支付完回调地址',
  `url` varchar(255) DEFAULT NULL COMMENT '支付完回调地址',
  `from_order` varchar(32) DEFAULT '' COMMENT '订单来源',
  `from_caractivityid` int(11) DEFAULT '0' COMMENT '来源活动车款ID',
  `from_activityid` int(11) DEFAULT '0' COMMENT '订单来源活动id',
  `ticket_number` varchar(32) DEFAULT '' COMMENT '优惠券号',
  `user_remark` varchar(255) DEFAULT '' COMMENT '用户备注',
  `cus_name` varchar(32) DEFAULT NULL COMMENT '客服姓名',
  `cus_remark` varchar(255) DEFAULT NULL COMMENT '客服备注',
  `cus_character` varchar(32) DEFAULT NULL COMMENT '客服-角色判读',
  `ID_imgurl1` varchar(255) DEFAULT NULL COMMENT '身份证正面图片',
  `ID_imgurl2` varchar(255) DEFAULT NULL COMMENT '身份证反面',
  `low_price` varchar(20) DEFAULT NULL COMMENT '成交底价',
  `low_price_city_name` varchar(255) DEFAULT NULL COMMENT '成交底价城市名称',
  `car_special_id` varchar(32) DEFAULT NULL COMMENT '特价车ID',
  `car_price_id` varchar(32) DEFAULT NULL COMMENT '底价车id',
  `name_4s` varchar(50) DEFAULT '' COMMENT '4s店名',
  `id_4s` varchar(32) DEFAULT NULL COMMENT '4s店id',
  `isread` tinyint(4) DEFAULT '1' COMMENT '是否读 1：未读；2：已读',
  `credit_fee` int(8) DEFAULT '0' COMMENT '贷款手续费',
  `insurance` varchar(10) DEFAULT NULL COMMENT '是否购买保险',
  `compulsory_insurance` varchar(20) DEFAULT NULL COMMENT '交强险',
  `cess` varchar(20) DEFAULT NULL COMMENT '税率',
  `licensing_fees` varchar(20) DEFAULT NULL COMMENT '上牌费',
  `travel_tax` varchar(20) DEFAULT NULL COMMENT '车船使用税',
  `car_loss_dang` varchar(20) DEFAULT NULL COMMENT '车辆损失险',
  `liability` varchar(20) DEFAULT NULL COMMENT '第三方责任险',
  `car_liability` varchar(20) DEFAULT NULL COMMENT '车上人员责任险',
  `deductible_special` varchar(20) DEFAULT NULL COMMENT '附加不计免赔特约',
  `is_distribute` int(1) DEFAULT '0' COMMENT '是否是重新分配的订单（0：未分配过，1，重新分配过）',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` varchar(13) DEFAULT NULL COMMENT '更新时间',
  `updatetime_3` varchar(13) DEFAULT NULL COMMENT '订单详情对应2的更新时间',
  `updateuser_3` varchar(32) DEFAULT NULL COMMENT '订单详情对应2的更新人',
  `updateuser_4` varchar(32) DEFAULT NULL COMMENT '订单详情对应3的更新人',
  `updatetime_4` varchar(13) DEFAULT NULL COMMENT '订单详情对应3的更新时间',
  `updateuser_5` varchar(32) DEFAULT NULL COMMENT '订单详情对应4的更新人',
  `updatetime_5` varchar(13) DEFAULT NULL COMMENT '订单详情对应4的更新时间',
  PRIMARY KEY (`id`),
  KEY `out_trade_no` (`out_trade_no`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=149 DEFAULT CHARSET=utf8 COMMENT='询价定车支付表';

-- ----------------------------
-- Table structure for gch_pay_area_low_price
-- ----------------------------
DROP TABLE IF EXISTS `gch_pay_area_low_price`;
CREATE TABLE `gch_pay_area_low_price` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `pay_id` varchar(32) NOT NULL COMMENT '支付订单id',
  `car_price_id` varchar(32) NOT NULL COMMENT '报价id',
  `id_4s` varchar(32) DEFAULT NULL,
  `pay_low_price` int(10) NOT NULL COMMENT '支付的最低价',
  `area_name` varchar(20) NOT NULL COMMENT '区域名称',
  `sales_area_level` int(10) NOT NULL COMMENT '销售区域权重',
  `pay_obj` tinyint(1) NOT NULL COMMENT '支付对象（1：询价 2：定金）',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `card_place` varchar(255) DEFAULT NULL COMMENT '上牌地(如果不填代表获取此底价车全部上牌地)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单中区域最低价表';

-- ----------------------------
-- Table structure for gch_pay_cus
-- ----------------------------
DROP TABLE IF EXISTS `gch_pay_cus`;
CREATE TABLE `gch_pay_cus` (
  `id` varchar(32) NOT NULL COMMENT 'id',
  `pay_id` varchar(32) NOT NULL COMMENT '订单id',
  `user_activity_id` varchar(32) DEFAULT NULL COMMENT '报名名用户ID',
  `type` int(8) DEFAULT '1' COMMENT '类型（1：客服判别操作，2：客服兑换操作）',
  `cus_name` varchar(10) DEFAULT NULL COMMENT '客服姓名',
  `cus_remark` varchar(255) DEFAULT NULL COMMENT '客服备注',
  `cus_character` varchar(32) DEFAULT NULL COMMENT '客服-角色判读',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COMMENT='积分详情表';

-- ----------------------------
-- Table structure for gch_pay_to_user
-- ----------------------------
DROP TABLE IF EXISTS `gch_pay_to_user`;
CREATE TABLE `gch_pay_to_user` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT 'id',
  `pay_id` varchar(32) DEFAULT NULL COMMENT 'gch_pay 的订单id',
  `user_id` varchar(32) DEFAULT NULL COMMENT '4s店id',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `isdelete` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_price_trend
-- ----------------------------
DROP TABLE IF EXISTS `gch_price_trend`;
CREATE TABLE `gch_price_trend` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `car_price_id` varchar(32) DEFAULT NULL COMMENT '所属底价车',
  `exterior_color_id` varchar(32) DEFAULT NULL COMMENT '外观颜色id',
  `interior_color_id` varchar(32) DEFAULT NULL COMMENT '内饰颜色id',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款ID',
  `year` int(11) DEFAULT NULL COMMENT '年份',
  `month` int(11) DEFAULT NULL COMMENT '月份',
  `price` varchar(10) DEFAULT NULL,
  `low_price` varchar(10) DEFAULT NULL COMMENT '价格',
  `discount` varchar(10) DEFAULT NULL,
  `createtime` varchar(32) DEFAULT '' COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `createip` varchar(32) DEFAULT NULL,
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` varchar(32) DEFAULT '' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `updateip` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `car_price_id` (`car_price_id`) USING HASH,
  KEY `exterior_color_id` (`exterior_color_id`) USING HASH,
  KEY `interior_color_id` (`interior_color_id`) USING HASH,
  KEY `car_id` (`car_id`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=90501 DEFAULT CHARSET=utf8 COMMENT='价格趋势表';

-- ----------------------------
-- Table structure for gch_province
-- ----------------------------
DROP TABLE IF EXISTS `gch_province`;
CREATE TABLE `gch_province` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `area_id` varchar(32) NOT NULL COMMENT '区域ID',
  `province_name` varchar(50) DEFAULT NULL COMMENT '省份名称',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否显示',
  PRIMARY KEY (`id`),
  KEY `isdelete` (`isdelete`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='4S店所在省份';

-- ----------------------------
-- Table structure for gch_receipt_address
-- ----------------------------
DROP TABLE IF EXISTS `gch_receipt_address`;
CREATE TABLE `gch_receipt_address` (
  `id` varchar(32) NOT NULL COMMENT '主键ID',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户ID',
  `role` tinyint(4) DEFAULT NULL COMMENT '用户角色',
  `receiver` varchar(32) DEFAULT NULL COMMENT '收货人',
  `telphone` varchar(20) DEFAULT NULL COMMENT '联系方式',
  `province_id` varchar(32) DEFAULT NULL COMMENT '省份id',
  `receipt_province` varchar(50) DEFAULT NULL COMMENT '收货省份',
  `city_id` varchar(32) DEFAULT NULL COMMENT '城市id',
  `receipt_city` varchar(50) DEFAULT NULL COMMENT '收货城市',
  `quarter_id` varchar(32) DEFAULT NULL COMMENT '地区id',
  `receipt_quarter` varchar(50) DEFAULT NULL COMMENT '收货区域',
  `receipt_address` varchar(255) DEFAULT NULL COMMENT '详细收货地址',
  `status` tinyint(4) DEFAULT NULL COMMENT '选中状态；1：选中 2：未选中',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_role
-- ----------------------------
DROP TABLE IF EXISTS `gch_role`;
CREATE TABLE `gch_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='后台角色表';

-- ----------------------------
-- Table structure for gch_role_manager
-- ----------------------------
DROP TABLE IF EXISTS `gch_role_manager`;
CREATE TABLE `gch_role_manager` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_sales_area
-- ----------------------------
DROP TABLE IF EXISTS `gch_sales_area`;
CREATE TABLE `gch_sales_area` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `sales_area_name` varchar(20) NOT NULL COMMENT '销售区域名称',
  `sales_area_level` int(10) NOT NULL COMMENT '销售区域权重 1、全国 2、省份 3、城市',
  `car_price_id` varchar(32) DEFAULT NULL COMMENT '低价车id',
  `car_special_id` varchar(32) DEFAULT NULL COMMENT '特价车ID',
  `type` int(2) NOT NULL COMMENT '销售区域类别1 底价车，2特价车',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售区域表（底价车、特价车）';

-- ----------------------------
-- Table structure for gch_score
-- ----------------------------
DROP TABLE IF EXISTS `gch_score`;
CREATE TABLE `gch_score` (
  `id` varchar(32) NOT NULL COMMENT 'id',
  `user_id` varchar(32) NOT NULL COMMENT '关联user的id',
  `role` tinyint(4) DEFAULT NULL COMMENT '用户角色',
  `score` int(6) NOT NULL COMMENT '积分',
  `info` varchar(255) NOT NULL COMMENT '积分增减说明',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT '' COMMENT '创建人',
  `isdelete` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT '' COMMENT '修改人',
  `flag` int(8) DEFAULT '0' COMMENT '来源标志1：积分兑换订单,2：询价/订车订单,3：商城订单，4：分享活动分享晒单加200积分5，分享活动分享晒单用户前15个被扫加20积分，6好友买车成功送500积分',
  `from_id` varchar(32) DEFAULT '' COMMENT '来源ID（有的是微信用户的openid）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分详情表';

-- ----------------------------
-- Table structure for gch_score_exchange
-- ----------------------------
DROP TABLE IF EXISTS `gch_score_exchange`;
CREATE TABLE `gch_score_exchange` (
  `id` varchar(32) NOT NULL COMMENT 'id主键',
  `out_trade_no` varchar(100) DEFAULT NULL COMMENT '订单号',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户id',
  `role` tinyint(4) DEFAULT NULL,
  `telphone` varchar(20) DEFAULT NULL COMMENT '兑换人的手机号',
  `goods_id` varchar(32) DEFAULT NULL COMMENT '积分商品id',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '积分商品名称',
  `score_value` int(11) DEFAULT NULL COMMENT '消耗的积分值',
  `address_id` varchar(32) DEFAULT NULL COMMENT '收货地址',
  `status` tinyint(2) DEFAULT '1' COMMENT '兑换状态：1未兑换，2已兑换',
  `cus_name` varchar(32) DEFAULT '' COMMENT '操作人姓名',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_search_key
-- ----------------------------
DROP TABLE IF EXISTS `gch_search_key`;
CREATE TABLE `gch_search_key` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `search_key` varchar(50) DEFAULT NULL COMMENT '关键字',
  `search_count` int(11) DEFAULT '0' COMMENT '搜索量',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='搜索关键词';

-- ----------------------------
-- Table structure for gch_service
-- ----------------------------
DROP TABLE IF EXISTS `gch_service`;
CREATE TABLE `gch_service` (
  `service_num` int(11) NOT NULL COMMENT '服务号 (1.4S店每月不同底价车款获得不同积分 2.4s店每季度不达到底价车标准扣分)',
  `service_name` varchar(100) DEFAULT NULL COMMENT '服务名称',
  `service_time` varchar(20) NOT NULL COMMENT '操作日期',
  `start_time` varchar(32) DEFAULT NULL COMMENT '开始时间',
  `end_time` varchar(32) DEFAULT NULL COMMENT '结束时间',
  `service_describe` varchar(100) DEFAULT NULL COMMENT '服务描述',
  `success_status` int(11) NOT NULL COMMENT '成功状态 1:未成功 2-8:阶段成功  9:全部成功',
  KEY `fk5-1` (`service_time`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_share_logs
-- ----------------------------
DROP TABLE IF EXISTS `gch_share_logs`;
CREATE TABLE `gch_share_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wx_open_id` varchar(100) DEFAULT NULL COMMENT '微信OPENIDA',
  `pay_id` varchar(32) DEFAULT NULL COMMENT '支付表ID',
  `image_url` varchar(100) DEFAULT NULL COMMENT '图片URL',
  `auth_price` int(11) DEFAULT NULL COMMENT '官方价',
  `bare_price` char(10) DEFAULT NULL COMMENT '裸车价',
  `cess` varchar(20) DEFAULT NULL COMMENT '购置税',
  `compulsory_insuran` varchar(20) DEFAULT NULL COMMENT '交强险',
  `business` varchar(20) DEFAULT NULL COMMENT '商业保险',
  `travel_tax` varchar(20) DEFAULT NULL COMMENT '车船使用税',
  `licensing_fees` varchar(20) DEFAULT NULL COMMENT '上牌税',
  `price` varchar(20) DEFAULT NULL COMMENT '其他价格',
  `low_price` varchar(20) DEFAULT NULL COMMENT '购车惠价格',
  `discount` varchar(20) DEFAULT NULL COMMENT '优惠价格',
  `gifts` varchar(100) DEFAULT NULL COMMENT '赠品',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_special_price_car
-- ----------------------------
DROP TABLE IF EXISTS `gch_special_price_car`;
CREATE TABLE `gch_special_price_car` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `user_id` varchar(32) NOT NULL COMMENT '关联用户ID',
  `interior_color_id` varchar(32) DEFAULT NULL COMMENT '内饰颜色ID',
  `exterior_color_id` varchar(32) NOT NULL COMMENT '外观颜色ID',
  `car_image` varchar(500) NOT NULL COMMENT '车款图片',
  `car_id` varchar(32) NOT NULL COMMENT '汽车ID',
  `client` int(4) unsigned DEFAULT '1' COMMENT '客户端(1:pc,2:手机web端)',
  `price` int(10) DEFAULT NULL COMMENT '价格',
  `special_price` int(10) NOT NULL COMMENT '特价',
  `start_date` datetime NOT NULL COMMENT '开始时间',
  `end_date` datetime NOT NULL COMMENT '结束时间',
  `number` int(10) unsigned NOT NULL COMMENT '活动数量',
  `status` tinyint(2) DEFAULT '1' COMMENT '特价车状态（1：审核中，2：未开始，3：在售，4：停售，5：过期，6：审核失败）',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注(审核不通过）',
  `description` text COMMENT '活动说明',
  `attention_count` int(11) DEFAULT NULL COMMENT '关注度',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='特价车';

-- ----------------------------
-- Table structure for gch_special_price_car_trend
-- ----------------------------
DROP TABLE IF EXISTS `gch_special_price_car_trend`;
CREATE TABLE `gch_special_price_car_trend` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `special_price_car` varchar(32) NOT NULL DEFAULT '' COMMENT 'gch_special_price_car表的ID',
  `user_id` varchar(32) NOT NULL COMMENT '关联用户ID',
  `interior_color_id` varchar(32) DEFAULT NULL COMMENT '内饰颜色ID',
  `exterior_color_id` varchar(32) NOT NULL COMMENT '外观颜色ID',
  `car_image` varchar(500) NOT NULL COMMENT '车款图片',
  `car_id` varchar(32) NOT NULL COMMENT '汽车ID',
  `price` int(10) DEFAULT NULL COMMENT '价格',
  `special_price` int(10) NOT NULL COMMENT '特价',
  `start_date` datetime NOT NULL COMMENT '开始时间',
  `end_date` datetime NOT NULL COMMENT '结束时间',
  `number` int(10) unsigned NOT NULL COMMENT '活动数量',
  `status` tinyint(2) DEFAULT '1' COMMENT '特价车状态（1：审核中，2：未开始，3：在售，4：停售，5：过期，6：审核失败）',
  `description` text COMMENT '活动说明',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='特价车';

-- ----------------------------
-- Table structure for gch_ticket
-- ----------------------------
DROP TABLE IF EXISTS `gch_ticket`;
CREATE TABLE `gch_ticket` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `ticket_name` varchar(50) DEFAULT NULL COMMENT '券名称',
  `money` int(32) DEFAULT NULL COMMENT '优惠券面额',
  `validity` int(255) DEFAULT NULL COMMENT '有效期',
  `release_amount` int(10) DEFAULT '0' COMMENT '数量不限时，发布500张的数量',
  `amount` int(10) DEFAULT '0' COMMENT '优惠券数量（0不限，n数量）',
  `release_status` int(1) DEFAULT '1' COMMENT '发布状态（1未发布，2已发布）',
  `ticket_prefix` varchar(10) DEFAULT NULL COMMENT '券号生成的前缀',
  `remark` text COMMENT '备注',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_ticket_pool
-- ----------------------------
DROP TABLE IF EXISTS `gch_ticket_pool`;
CREATE TABLE `gch_ticket_pool` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `ticket_id` int(11) DEFAULT NULL COMMENT '券id',
  `ticket_number` varchar(32) DEFAULT NULL COMMENT '券号',
  `ticket_name` varchar(50) DEFAULT NULL COMMENT '券名称',
  `money` int(32) DEFAULT NULL COMMENT '优惠券面额',
  `validity` int(255) DEFAULT NULL COMMENT '有效期',
  `remark` text COMMENT '备注',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3001 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_ticket_user
-- ----------------------------
DROP TABLE IF EXISTS `gch_ticket_user`;
CREATE TABLE `gch_ticket_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户id',
  `ticket_id` int(32) DEFAULT NULL COMMENT '优惠券id',
  `ticket_number` varchar(32) DEFAULT NULL COMMENT '券编号',
  `ticket_name` varchar(32) DEFAULT NULL COMMENT '券名称',
  `money` int(32) DEFAULT NULL COMMENT '优惠券面额',
  `validity` int(10) DEFAULT '0' COMMENT '有效期',
  `end_time` int(32) DEFAULT NULL COMMENT '券的过期日期',
  `pay_id` varchar(32) DEFAULT NULL COMMENT '订单id',
  `status` int(1) DEFAULT '1' COMMENT '使用状态（1未使用，2已使用，3已过期，4已占用）',
  `from_ticket` varchar(255) DEFAULT NULL COMMENT '优惠券来源',
  `activity_id` int(16) DEFAULT '0' COMMENT '活动ID',
  `remark` text COMMENT '备注',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_user
-- ----------------------------
DROP TABLE IF EXISTS `gch_user`;
CREATE TABLE `gch_user` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '主键',
  `user_name` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '登录名',
  `password` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '登陆密码(MD5)',
  `role` int(11) DEFAULT NULL COMMENT '身份1、普通用户 2、4S店会员(关联到4S店自定管理4S店主键) 3、经销商',
  `tel` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT '电话',
  `email` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '邮箱',
  `sex` int(11) DEFAULT NULL COMMENT '性别 0、男 1、女',
  `createtime` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '创建时间',
  `createuser` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '创建人',
  `updatetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `updateuser` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '修改人',
  `logintime` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '登录IP',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `status` smallint(1) DEFAULT NULL COMMENT '用户状态',
  `name_4s` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '4S店名称',
  `brand_4s` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '4S店主营品牌',
  `contacts` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '联系人',
  `addr` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '地址',
  `city_id` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '城市ID',
  `dealer_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '经销商名称',
  `head_url` varchar(240) COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  `nick` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '昵称',
  `my_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '我的邀请码',
  `binding_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '绑定邀请码',
  `total_jifen` int(7) unsigned DEFAULT '0' COMMENT '用户积分',
  `jifen_sign` varchar(2) COLLATE utf8_bin DEFAULT '0',
  `wx_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '微信开放用户ID',
  `power` int(11) DEFAULT NULL COMMENT '1：报价权限，2：4s店名称地址权限，4：库存权限；权限值可叠加。',
  `qq_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'QQ互联用户open_id',
  `month_car_num` int(11) NOT NULL DEFAULT '0' COMMENT '每个月被标为底价的车款数',
  `total_car_num` int(5) NOT NULL DEFAULT '0',
  `quarter_car_num` int(11) NOT NULL DEFAULT '0' COMMENT '每个季度被标为底价的车款数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表';

-- ----------------------------
-- Table structure for gch_user_4s
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_4s`;
CREATE TABLE `gch_user_4s` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '主键',
  `user_name` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '登录名',
  `password` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '登陆密码(MD5)',
  `role` int(11) DEFAULT NULL COMMENT '身份1、普通用户 2、4S店会员(关联到4S店自定管理4S店主键) 3、经销商',
  `tel` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT '电话',
  `email` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '邮箱',
  `status` smallint(1) DEFAULT NULL COMMENT '用户状态',
  `head_url` varchar(240) COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  `nick` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '昵称',
  `name_4s` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '4S店名称',
  `brand_4s` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '4S店主营品牌',
  `contacts` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '联系人',
  `addr` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '地址',
  `city_id` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '城市ID',
  `my_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '我的邀请码',
  `binding_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '绑定邀请码',
  `total_jifen` int(7) unsigned DEFAULT '0' COMMENT '用户积分',
  `jifen_sign` varchar(2) COLLATE utf8_bin DEFAULT '0',
  `exchange_usedtimes` int(8) DEFAULT '0' COMMENT '当月积分已使用次数',
  `exchange_alltimes` int(8) DEFAULT '0' COMMENT '当月积分总兑换次数',
  `wx_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '微信开放用户ID',
  `qq_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'QQ互联用户open_id',
  `power` int(11) DEFAULT NULL COMMENT '1：报价权限，2：4s店名称地址权限，4：库存权限；权限值可叠加。',
  `month_car_num` int(11) NOT NULL DEFAULT '0' COMMENT '每个月被标为底价的车款数',
  `total_car_num` int(5) NOT NULL DEFAULT '0',
  `quarter_car_num` int(11) NOT NULL DEFAULT '0' COMMENT '每个季度被标为底价的车款数',
  `logintime` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `loginip` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表';

-- ----------------------------
-- Table structure for gch_user_4s_brand
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_4s_brand`;
CREATE TABLE `gch_user_4s_brand` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `brand_id` varchar(32) DEFAULT NULL COMMENT '品牌ID',
  `user_4s_id` varchar(32) DEFAULT NULL COMMENT '4S店用户ID',
  `create_time` varchar(15) DEFAULT NULL COMMENT '添加时间',
  `create_user` varchar(32) DEFAULT NULL COMMENT '创建人ID',
  `update_time` varchar(15) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `isdelete` smallint(1) unsigned zerofill DEFAULT '0' COMMENT '是否删除(0;没有删除，1：删除)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_user_activity
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_activity`;
CREATE TABLE `gch_user_activity` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT '主键id',
  `source` int(1) DEFAULT '1' COMMENT '来源：1：web端，2：微信端',
  `user_name` varchar(32) DEFAULT NULL COMMENT '用户名',
  `tel` varchar(11) DEFAULT NULL COMMENT '手机号',
  `activity_price` varchar(10) DEFAULT NULL COMMENT '活动价',
  `brand_id` varchar(32) DEFAULT NULL COMMENT '品牌id',
  `brand_name` varchar(255) DEFAULT NULL COMMENT '品牌名称',
  `car_model_id` varchar(32) DEFAULT NULL COMMENT '车型id',
  `car_model_name` varchar(255) DEFAULT NULL COMMENT '车型名称',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款id',
  `car_name` varchar(255) DEFAULT NULL COMMENT '车款名称',
  `exterior_color_id` varchar(32) DEFAULT NULL COMMENT '外观颜色id',
  `exterior_color_name` varchar(255) DEFAULT NULL COMMENT '外观颜色名称',
  `interior_color_id` varchar(32) DEFAULT NULL COMMENT '内饰颜色id',
  `interior_color_name` varchar(255) DEFAULT NULL COMMENT '内饰颜色名称',
  `activity_number` varchar(50) DEFAULT NULL COMMENT '活动编号',
  `activity_name` varchar(50) DEFAULT NULL COMMENT '活动名称',
  `flag` int(1) DEFAULT NULL COMMENT '活动类型：1：立即报名，2：特价车，3：立即购买，4：马上抢',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '修改人',
  `reward_type` varchar(10) DEFAULT NULL COMMENT '奖励的标识(积分,优惠券)',
  `reward_id` varchar(32) DEFAULT NULL COMMENT '奖励的唯一标识',
  `remarks` varchar(512) DEFAULT NULL COMMENT '备注',
  `status_track` int(1) DEFAULT '1' COMMENT '询价订单状态（1：未跟踪，2：已跟踪）',
  `cus_name` varchar(32) DEFAULT '' COMMENT '客服姓名',
  `cus_remark` varchar(255) DEFAULT NULL COMMENT '客服备注',
  `cus_character` varchar(32) DEFAULT '' COMMENT '客服-角色判读（1：用户，2：内部）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_user_attention_car_model
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_attention_car_model`;
CREATE TABLE `gch_user_attention_car_model` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `user_id` varchar(32) DEFAULT NULL COMMENT '关注用户',
  `car_model_id` varchar(32) DEFAULT NULL COMMENT '所属车型',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间(关注时间)',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户关注车型表';

-- ----------------------------
-- Table structure for gch_user_business
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_business`;
CREATE TABLE `gch_user_business` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '主键',
  `user_name` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '登录名',
  `password` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '登陆密码(MD5)',
  `role` int(11) DEFAULT NULL COMMENT '身份1、普通用户 2、4S店会员(关联到4S店自定管理4S店主键) 3、经销商',
  `tel` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT '电话',
  `email` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '邮箱',
  `head_url` varchar(240) COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  `nick` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '昵称',
  `status` smallint(1) DEFAULT NULL COMMENT '用户状态',
  `address` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '地址',
  `my_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '我的邀请码',
  `binding_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '绑定邀请码',
  `total_jifen` int(7) unsigned DEFAULT '0' COMMENT '用户积分',
  `jifen_sign` varchar(2) COLLATE utf8_bin DEFAULT '0',
  `wx_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '微信开放用户ID',
  `qq_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'QQ互联用户open_id',
  `power` int(11) DEFAULT NULL COMMENT '1：报价权限，2：4s店名称地址权限，4：库存权限；权限值可叠加。',
  `logintime` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '登录IP',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表';

-- ----------------------------
-- Table structure for gch_user_car
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_car`;
CREATE TABLE `gch_user_car` (
  `id` varchar(32) NOT NULL COMMENT 'id',
  `user_id` varchar(32) DEFAULT NULL COMMENT '用户id',
  `car_id` varchar(32) DEFAULT NULL COMMENT '车款id',
  `month_status` tinyint(5) DEFAULT NULL COMMENT '月份（底价次数标志）',
  `quarter_status` tinyint(5) DEFAULT NULL COMMENT '季度（底价次数标志）',
  `month_car_price_num` int(11) DEFAULT '0' COMMENT '该车款下的最低价数量',
  `quarter_car_price_num` int(11) DEFAULT NULL,
  `isdelete` int(11) DEFAULT NULL COMMENT '是否删除',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='4S用户底价标志表（积分使用）';

-- ----------------------------
-- Table structure for gch_user_dealer
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_dealer`;
CREATE TABLE `gch_user_dealer` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '主键',
  `user_name` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '登录名',
  `password` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '登陆密码(MD5)',
  `role` int(11) DEFAULT NULL COMMENT '身份1、普通用户 2、4S店会员(关联到4S店自定管理4S店主键) 3、经销商',
  `tel` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT '电话',
  `email` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '邮箱',
  `head_url` varchar(240) COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  `nick` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '昵称',
  `status` smallint(1) DEFAULT NULL COMMENT '用户状态',
  `contacts` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '联系人',
  `address` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '地址',
  `city_id` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '城市ID',
  `dealer_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '经销商名称',
  `my_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '我的邀请码',
  `binding_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '绑定邀请码',
  `total_jifen` int(7) unsigned DEFAULT '0' COMMENT '用户积分',
  `jifen_sign` varchar(2) COLLATE utf8_bin DEFAULT '0',
  `wx_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '微信开放用户ID',
  `qq_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'QQ互联用户open_id',
  `power` int(11) DEFAULT NULL COMMENT '1：报价权限，2：4s店名称地址权限，4：库存权限；权限值可叠加。',
  `logintime` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `loginip` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表';

-- ----------------------------
-- Table structure for gch_user_general
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_general`;
CREATE TABLE `gch_user_general` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '主键',
  `user_name` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '登录名',
  `password` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '登陆密码(MD5)',
  `role` int(11) DEFAULT NULL COMMENT '身份1、普通用户 2、4S店会员(关联到4S店自定管理4S店主键) 3、经销商',
  `tel` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT '电话',
  `email` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '邮箱',
  `head_url` varchar(240) COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  `name` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `nick` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '真实姓名',
  `sex` int(11) DEFAULT NULL COMMENT '性别 0、男 1、女',
  `birthday` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '生日',
  `province_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '所在地-省份',
  `city_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '所在地-城市',
  `district_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '区id',
  `address` varchar(255) COLLATE utf8_bin DEFAULT '' COMMENT '详细地址',
  `postcode` varchar(11) COLLATE utf8_bin DEFAULT NULL COMMENT '邮编',
  `status` smallint(1) DEFAULT NULL COMMENT '用户状态（0，锁定；1，正常）',
  `my_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '我的邀请码',
  `binding_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '绑定邀请码',
  `total_coin` int(7) DEFAULT '0' COMMENT '车币',
  `total_jifen` int(7) unsigned DEFAULT '0' COMMENT '用户积分',
  `balance` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT '余额',
  `jifen_sign` varchar(2) COLLATE utf8_bin DEFAULT '0',
  `wx_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '微信开放用户ID',
  `qq_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'QQ互联用户open_id',
  `logintime` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `loginip` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `remark` text COLLATE utf8_bin COMMENT '备注',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `activity_attention_num` int(6) unsigned DEFAULT '0' COMMENT '活动中通过该用户关注我们公众号的总数',
  `activity_register_num` int(6) unsigned DEFAULT '0' COMMENT '通过活动注册的总数',
  `buy_car_num` int(6) unsigned DEFAULT '0' COMMENT '通过该用户买车的用户的总数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `wx_open_id` (`wx_open_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表';

-- ----------------------------
-- Table structure for gch_user_general_copy
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_general_copy`;
CREATE TABLE `gch_user_general_copy` (
  `id` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '主键',
  `user_name` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '登录名',
  `password` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '登陆密码(MD5)',
  `role` int(11) DEFAULT NULL COMMENT '身份1、普通用户 2、4S店会员(关联到4S店自定管理4S店主键) 3、经销商',
  `tel` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT '电话',
  `email` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT '邮箱',
  `head_url` varchar(240) COLLATE utf8_bin DEFAULT NULL COMMENT '头像',
  `name` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `nick` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '真实姓名',
  `sex` int(11) DEFAULT NULL COMMENT '性别 0、男 1、女',
  `birthday` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '生日',
  `province_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '所在地-省份',
  `city_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '所在地-城市',
  `postcode` varchar(11) COLLATE utf8_bin DEFAULT NULL COMMENT '邮编',
  `status` smallint(1) DEFAULT NULL COMMENT '用户状态',
  `my_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '我的邀请码',
  `binding_num` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '绑定邀请码',
  `total_coin` int(7) DEFAULT '0' COMMENT '车币',
  `total_jifen` int(7) unsigned DEFAULT '0' COMMENT '用户积分',
  `balance` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT '余额',
  `jifen_sign` varchar(2) COLLATE utf8_bin DEFAULT '0',
  `wx_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '微信开放用户ID',
  `qq_open_id` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT 'QQ互联用户open_id',
  `logintime` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `loginip` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `remark` text COLLATE utf8_bin COMMENT '备注',
  `createtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wx_open_id` (`wx_open_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表';

-- ----------------------------
-- Table structure for gch_user_message
-- ----------------------------
DROP TABLE IF EXISTS `gch_user_message`;
CREATE TABLE `gch_user_message` (
  `id` varchar(32) NOT NULL COMMENT '主键',
  `user_id` varchar(32) DEFAULT NULL COMMENT '所属用户',
  `type` int(11) DEFAULT NULL COMMENT '消息类型0、关注底价车消息 1、站内短信 2、其他',
  `json` varchar(2000) DEFAULT NULL COMMENT '对应数据(需要处理对应数据)',
  `isread` tinyint(2) DEFAULT NULL COMMENT '1 未读 ，2已读',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `createuser` varchar(32) DEFAULT NULL COMMENT '创建人',
  `isdelete` int(11) DEFAULT '0' COMMENT '是否删除',
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `updateuser` varchar(32) DEFAULT NULL COMMENT '修改人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息通知';

-- ----------------------------
-- Table structure for gch_website_basic
-- ----------------------------
DROP TABLE IF EXISTS `gch_website_basic`;
CREATE TABLE `gch_website_basic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_title` varchar(100) DEFAULT NULL COMMENT '网站标题',
  `site_description` varchar(200) DEFAULT NULL COMMENT '网站描述',
  `site_status` smallint(5) unsigned DEFAULT '1' COMMENT '站点状态(0:关闭，1:开启)',
  `login_verify` smallint(5) unsigned DEFAULT '1' COMMENT '网站验证(0:关闭，1:开启)',
  `copr` varchar(50) DEFAULT NULL COMMENT '版权信息',
  `site_key` varchar(100) DEFAULT NULL COMMENT '网站关键字',
  `ICP` varchar(40) DEFAULT NULL COMMENT 'ICP备案',
  `admin_colors` varchar(20) DEFAULT NULL COMMENT '后台色系',
  `site_logo` varchar(255) DEFAULT NULL COMMENT '站点logo',
  `site_reg_status` smallint(5) unsigned DEFAULT '1' COMMENT '前台注册开关(0:关闭,1:开启)',
  `admin_login_status` smallint(5) unsigned DEFAULT '1' COMMENT '后台登录控制(0：关闭登录,1：开启登录)',
  `bbs_status` smallint(5) unsigned DEFAULT '1' COMMENT '官方论坛开关(0:关闭,1:开启)',
  `admin_login_bgimg` varchar(255) DEFAULT NULL COMMENT '后台登录界面背景图',
  `_MASK_FROM_V2` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `IDX_d39dea54599d424da4b827187ad7f1f5` (`_MASK_FROM_V2`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='站点配置';

-- ----------------------------
-- Table structure for gch_wxpay_package
-- ----------------------------
DROP TABLE IF EXISTS `gch_wxpay_package`;
CREATE TABLE `gch_wxpay_package` (
  `id` int(16) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `pay_id` varchar(32) NOT NULL,
  `out_trade_no` varchar(32) DEFAULT NULL COMMENT '本系统的订单号',
  `transaction_id` varchar(32) DEFAULT NULL COMMENT '微信支付订单号',
  `total_fee` int(16) DEFAULT NULL COMMENT '订单总金额',
  `trade_type` varchar(16) DEFAULT NULL COMMENT '交易类型',
  `bank_type` varchar(16) DEFAULT NULL COMMENT '付款银行',
  `time_end` varchar(14) DEFAULT NULL,
  `package` text COMMENT '整个返回包',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=753 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_wx_buy_log
-- ----------------------------
DROP TABLE IF EXISTS `gch_wx_buy_log`;
CREATE TABLE `gch_wx_buy_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fromopenid` varchar(64) DEFAULT NULL COMMENT '被扫码人的openid',
  `myopenid` varchar(64) DEFAULT NULL COMMENT '我自己的openid',
  `activity_name` varchar(30) DEFAULT NULL COMMENT '活动的名称',
  `createtime` varchar(30) DEFAULT NULL COMMENT '创建的时间',
  `nickname` varchar(32) DEFAULT NULL COMMENT '扫码人的昵称',
  `order_id` varchar(32) DEFAULT NULL COMMENT '订单ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_wx_news_log
-- ----------------------------
DROP TABLE IF EXISTS `gch_wx_news_log`;
CREATE TABLE `gch_wx_news_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(64) DEFAULT NULL COMMENT '发送请求人的唯一标识',
  `msgtype` varchar(32) DEFAULT NULL COMMENT '发送请求的类型',
  `createtime` varchar(20) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL COMMENT '发送请求的类型',
  `event` varchar(20) DEFAULT NULL COMMENT '发送事件的类型',
  `eventkey` varchar(64) DEFAULT NULL COMMENT '事件标识',
  `ticket` varchar(255) DEFAULT NULL COMMENT '二维码标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_wx_reg_log
-- ----------------------------
DROP TABLE IF EXISTS `gch_wx_reg_log`;
CREATE TABLE `gch_wx_reg_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fromopenid` varchar(64) DEFAULT NULL COMMENT '被扫码人的openid',
  `myopenid` varchar(64) DEFAULT NULL COMMENT '我自己的openid',
  `activity_name` varchar(30) DEFAULT NULL COMMENT '活动的名称',
  `createtime` varchar(30) DEFAULT NULL COMMENT '创建的时间',
  `nickname` varchar(32) DEFAULT NULL COMMENT '扫码人的昵称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_wx_reply
-- ----------------------------
DROP TABLE IF EXISTS `gch_wx_reply`;
CREATE TABLE `gch_wx_reply` (
  `id` int(11) NOT NULL,
  `msgtype` varchar(20) DEFAULT NULL,
  `content` varchar(500) DEFAULT NULL,
  `event` varchar(20) DEFAULT NULL,
  `eventkey` varchar(100) DEFAULT NULL,
  `keywords` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gch_wx_scan_log
-- ----------------------------
DROP TABLE IF EXISTS `gch_wx_scan_log`;
CREATE TABLE `gch_wx_scan_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fromopenid` varchar(64) DEFAULT NULL COMMENT '被扫码人的openid',
  `myopenid` varchar(64) DEFAULT NULL COMMENT '我自己的openid',
  `activity_name` varchar(30) DEFAULT NULL COMMENT '活动的名称',
  `createtime` varchar(30) DEFAULT NULL COMMENT '创建的时间',
  `nickname` varchar(32) DEFAULT NULL COMMENT '扫码人的昵称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for test_min_price
-- ----------------------------
DROP TABLE IF EXISTS `test_min_price`;
CREATE TABLE `test_min_price` (
  `id` int(11) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `price_str` varchar(16) DEFAULT NULL,
  `carid` varchar(32) DEFAULT NULL,
  `id2` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for v3_user_device
-- ----------------------------
DROP TABLE IF EXISTS `v3_user_device`;
CREATE TABLE `v3_user_device` (
  `userid` varchar(32) NOT NULL,
  `deviceid` varchar(32) DEFAULT NULL COMMENT '由设备自己上报 暂不使用',
  `token` varchar(32) DEFAULT NULL,
  `token_creat_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `token_last_login_at` timestamp NULL DEFAULT NULL,
  `device_desc` varchar(512) DEFAULT NULL COMMENT 'web is user-agent  ',
  `device_platform` varchar(16) DEFAULT NULL COMMENT 'web app',
  `device_os` varchar(32) DEFAULT NULL,
  `device_browser` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ios android web';

-- ----------------------------
-- View structure for gch_view_actives_log
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_actives_log`;
CREATE ALGORITHM=UNDEFINED DEFINER=`gouchehui`@`%` SQL SECURITY DEFINER VIEW `gch_view_actives_log` AS select `gvcp`.`exterior_color_id` AS `exterior_color_id`,`gvcp`.`interior_color_id` AS `interior_color_id`,`gvcp`.`car_name` AS `car_name`,`gvcp`.`car_model_id` AS `car_model_id`,`gvcp`.`car_id` AS `car_id`,`gvcp`.`car_model_name` AS `car_model_name`,`gvcp`.`brand_id` AS `brand_id`,`gvcp`.`brand_name` AS `brand_name`,`gcl`.`id` AS `id`,`gcl`.`cpid` AS `cpid`,`gcl`.`user_id` AS `user_id`,`gcl`.`click_num` AS `click_num`,`gcl`.`create_time` AS `create_time`,`gcl`.`update_time` AS `update_time`,`gcl`.`ip` AS `IP`,`gcl`.`ip_click` AS `IP_click`,`gvcp`.`interior_color_name` AS `interior_color_name`,`gvcp`.`interior_color_value` AS `interior_color_value`,`gvcp`.`exterior_color_name` AS `exterior_color_name`,`gvcp`.`exterior_color_value` AS `exterior_color_value`,`gug`.`user_name` AS `user_name` from ((`gch_actives_log` `gcl` left join `gch_view_car_price` `gvcp` on((`gvcp`.`id` = `gcl`.`cpid`))) left join `gch_user_general` `gug` on((`gug`.`id` = `gcl`.`user_id`))) ;

-- ----------------------------
-- View structure for gch_view_brand_user
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_brand_user`;
CREATE ALGORITHM=UNDEFINED DEFINER=`gouchehui`@`%` SQL SECURITY DEFINER VIEW `gch_view_brand_user` AS select `gu4b`.`id` AS `id`,`gu4b`.`user_4s_id` AS `user_4s_id`,`gu4b`.`isdelete` AS `isdelete`,`gu4b`.`brand_id` AS `brand_id`,`gb`.`brand_name` AS `brand_name`,`gb`.`logo` AS `logo`,`gb`.`alif` AS `alif` from (`gch_user_4s_brand` `gu4b` left join `gch_brand` `gb` on((`gb`.`id` = `gu4b`.`brand_id`))) where (`gu4b`.`isdelete` = 0) ;

-- ----------------------------
-- View structure for gch_view_car
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_car`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gch_view_car` AS select `gc`.`id` AS `id`,`gc`.`access_quantity` AS `access_quantity`,`gc`.`car_name` AS `car_name`,`gc`.`imgurl` AS `imgurl`,`gc`.`createtime` AS `createtime`,`gc`.`createuser` AS `createuser`,`gc`.`isdelete` AS `isdelete`,`gc`.`updatetime` AS `updatetime`,`gc`.`updateuser` AS `updateuser`,`gc`.`car_model_id` AS `car_model_id`,`gcm`.`car_model_name` AS `car_model_name`,`gcm`.`car_type_id` AS `car_type_id`,`gct`.`car_type_name` AS `car_type_name`,`gcm`.`brand_id` AS `brand_id`,`gb`.`brand_name` AS `brand_name`,`gc`.`alif` AS `alif`,`gcm`.`imgurl` AS `car_model_imageurl`,`gc`.`xj_count` AS `car_xj_count`,`gc`.`description` AS `description`,`gcm`.`xj_count` AS `car_model_xj_count`,`gb`.`alif` AS `brand_alif`,`gb`.`access_quantity` AS `brand_access_quantity`,`gcm`.`alif` AS `model_alif`,`gc`.`order` AS `ORDER`,`gc`.`status` AS `car_status`,`gc`.`auth_price` AS `auth_price`,`gc`.`displacement` AS `displacement`,`gb`.`logo` AS `logo`,`gcm`.`access_quantity` AS `model_access_quantity` from (((`gch_car` `gc` left join `gch_car_model` `gcm` on((`gc`.`car_model_id` = `gcm`.`id`))) left join `gch_car_type` `gct` on((`gcm`.`car_type_id` = `gct`.`id`))) left join `gch_brand` `gb` on((`gcm`.`brand_id` = `gb`.`id`))) ;

-- ----------------------------
-- View structure for gch_view_car_bbs
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_car_bbs`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `gch_view_car_bbs` AS select `gch_car_life_bbs`.`id` AS `id`,`gch_car_life_bbs`.`type_id` AS `type_id`,`gch_car_life_bbs`.`user_id` AS `user_id`,`gch_car_life_bbs`.`title` AS `title`,`gch_car_life_bbs`.`contents` AS `contents`,`gch_car_life_bbs`.`image` AS `image`,`gch_car_life_bbs`.`club_id` AS `club_id`,`gch_car_life_bbs`.`car_own` AS `car_own`,`gch_car_life_bbs`.`click_amount` AS `click_amount`,`gch_car_life_bbs`.`comment_amount` AS `comment_amount`,`gch_car_life_bbs`.`thumbs_amount` AS `thumbs_amount`,`gch_car_life_bbs`.`createtime` AS `createtime`,`gch_car_life_bbs`.`createuser` AS `createuser`,`gch_car_life_bbs`.`isdelete` AS `isdelete`,`gch_car_life_bbs`.`updatetime` AS `updatetime`,`gch_car_life_bbs`.`updateuser` AS `updateuser`,`gch_user_general`.`user_name` AS `user_name`,`gch_car_life`.`type_name` AS `type_name`,`gch_car_life_bbs`.`display` AS `display`,`gch_car_life_bbs`.`check` AS `check`,`gch_car_life_bbs`.`place_hot` AS `place_hot`,`gch_car_life_bbs`.`place_index` AS `place_index`,`gch_car_life_bbs`.`car_id` AS `car_id`,`gch_view_car`.`car_name` AS `car_name`,`gch_view_car`.`brand_id` AS `brand_id`,`gch_view_car`.`brand_name` AS `brand_name`,`gch_view_car`.`car_model_id` AS `car_model_id`,`gch_view_car`.`car_model_name` AS `car_model_name`,`gch_car_life`.`pid` AS `pid`,`gch_car_life_bbs`.`check_fail` AS `check_fail`,`gch_car_life_bbs`.`summary` AS `summary`,`gch_car_life_bbs`.`final_price` AS `final_price` from (((`gch_car_life_bbs` left join `gch_user_general` on((`gch_car_life_bbs`.`user_id` = `gch_user_general`.`id`))) left join `gch_car_life` on((`gch_car_life_bbs`.`type_id` = `gch_car_life`.`id`))) left join `gch_view_car` on((`gch_car_life_bbs`.`car_id` = `gch_view_car`.`id`))) ;

-- ----------------------------
-- View structure for gch_view_car_color
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_car_color`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `gch_view_car_color` AS select `gvc`.`brand_id` AS `brand_id`,`gvc`.`brand_name` AS `brand_name`,`gvc`.`car_model_id` AS `car_model_id`,`gvc`.`car_model_name` AS `car_model_name`,`gvc`.`id` AS `car_id`,`gvc`.`car_name` AS `car_name`,`gve`.`id` AS `exterior_color_id`,`gve`.`color_name` AS `exterior_color_name`,`gvi`.`id` AS `interior_color_id`,`gvi`.`color_name` AS `interior_color_name`,`gve`.`isdelete` AS `isdelete`,`gvc`.`car_status` AS `car_status`,`gvc`.`brand_alif` AS `brand_alif`,`gvc`.`auth_price` AS `auth_price`,`gve`.`exterior_img` AS `exterior_img`,`gve`.`color_value` AS `exterior_color_value`,`gvi`.`color_value` AS `interior_color_value`,`gvc`.`displacement` AS `displacement` from ((`gch_view_exterior_color` `gve` left join `gch_view_interior_color` `gvi` on((`gvi`.`car_id` = `gve`.`car_id`))) left join `gch_view_car` `gvc` on((`gvc`.`id` = `gve`.`car_id`))) where ((`gvi`.`isdelete` = 0) and (`gvc`.`isdelete` = 0) and (`gve`.`isdelete` = 0)) ;

-- ----------------------------
-- View structure for gch_view_car_model
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_car_model`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gch_view_car_model` AS select `gcm`.`id` AS `id`,`gcm`.`access_quantity` AS `access_quantity`,`gcm`.`car_model_name` AS `car_model_name`,`gcm`.`imgurl` AS `imgurl`,`gcm`.`createtime` AS `createtime`,`gcm`.`createuser` AS `createuser`,`gcm`.`updatetime` AS `updatetime`,`gcm`.`updateuser` AS `updateuser`,`gcm`.`car_type_id` AS `car_type_id`,`gct`.`car_type_name` AS `car_type_name`,`gcm`.`brand_id` AS `brand_id`,`gb`.`brand_name` AS `brand_name`,`gcm`.`alif` AS `alif`,`gcm`.`isdelete` AS `isdelete`,`gcm`.`xj_count` AS `xj_count`,`gcm`.`attention_count` AS `attention_count`,`gcm`.`status` AS `car_model_status` from ((`gch_car_model` `gcm` left join `gch_car_type` `gct` on((`gcm`.`car_type_id` = `gct`.`id`))) left join `gch_brand` `gb` on((`gcm`.`brand_id` = `gb`.`id`))) ;

-- ----------------------------
-- View structure for gch_view_car_plan
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_car_plan`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `gch_view_car_plan` AS select `gch_car_plan`.`id` AS `id`,`gch_car_plan`.`user_id` AS `user_id`,`gch_car_plan`.`car_id` AS `car_id`,`gch_car_plan`.`buy_time` AS `buy_time`,`gch_car_plan`.`province_id` AS `province_id`,`gch_car_plan`.`city_id` AS `city_id`,`gch_car_plan`.`from_plan` AS `from_plan`,`gch_car_plan`.`createtime` AS `createtime`,`gch_car_plan`.`createuser` AS `createuser`,`gch_car_plan`.`isdelete` AS `isdelete`,`gch_car_plan`.`updatetime` AS `updatetime`,`gch_car_plan`.`updateuser` AS `updateuser`,`gch_user_general`.`user_name` AS `user_name`,`gch_view_car`.`car_name` AS `car_name`,`gch_view_car`.`car_model_id` AS `car_model_id`,`gch_view_car`.`car_model_name` AS `car_model_name`,`gch_view_car`.`brand_id` AS `brand_id`,`gch_view_car`.`brand_name` AS `brand_name` from ((`gch_car_plan` join `gch_user_general` on((`gch_user_general`.`id` = `gch_car_plan`.`user_id`))) join `gch_view_car` on((`gch_car_plan`.`car_id` = `gch_view_car`.`id`))) ;

-- ----------------------------
-- View structure for gch_view_car_price
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_car_price`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gch_view_car_price` AS select `gcp`.`id` AS `id`,`gcp`.`exterior_color_id` AS `exterior_color_id`,`gcp`.`interior_color_id` AS `interior_color_id`,`gcp`.`user_id` AS `user_id`,`gcp`.`access_quantity` AS `access_quantity`,`gcp`.`transactions_count` AS `transactions_count`,`gcp`.`asking_price_count` AS `asking_price_count`,`gcp`.`price` AS `price`,`gcp`.`discount` AS `discount`,`gcp`.`stock` AS `stock`,`gcp`.`onway` AS `onway`,`gcp`.`createtime` AS `createtime`,`gcp`.`createuser` AS `createuser`,`gcp`.`isdelete` AS `isdelete`,`gcp`.`updatetime` AS `updatetime`,`gcp`.`updateuser` AS `updateuser`,`gcp`.`low_price` AS `low_price`,`gvic`.`color_name` AS `interior_color_name`,`gvic`.`color_value` AS `interior_color_value`,`gvic`.`imgurl` AS `interior_color_imageurl`,`gvec`.`color_name` AS `exterior_color_name`,`gvec`.`color_value` AS `exterior_color_value`,`gvec`.`imgurl` AS `exterior_color_imgurl`,`gcp`.`car_id` AS `car_id`,`gvc`.`car_name` AS `car_name`,`gvc`.`car_model_id` AS `car_model_id`,`gvc`.`car_model_name` AS `car_model_name`,`gvc`.`car_type_id` AS `car_type_id`,`gvc`.`car_type_name` AS `car_type_name`,`gvc`.`car_model_imageurl` AS `car_model_imageurl`,`gvc`.`brand_id` AS `brand_id`,`gvc`.`brand_name` AS `brand_name`,`gvc`.`alif` AS `alif`,`gc`.`city_name` AS `city_name`,`gc`.`province_id` AS `province_id`,`gp`.`province_name` AS `province_name`,`gvc`.`car_xj_count` AS `car_xj_count`,`gcp`.`is_xunjia` AS `is_xunjia`,`gvc`.`brand_alif` AS `brand_alif`,`gvc`.`model_alif` AS `model_alif`,`gvc`.`car_model_xj_count` AS `car_model_xj_count`,`gcp`.`show_index` AS `show_index`,`gvc`.`brand_access_quantity` AS `brand_access_quantity`,`gvc`.`description` AS `description`,`gvc`.`car_status` AS `car_status`,`gch_user_4s`.`name_4s` AS `name_4s`,`gch_user_4s`.`brand_4s` AS `brand_4s`,`gch_user_4s`.`addr` AS `addr`,`gch_user_4s`.`city_id` AS `city_id`,`gvc`.`auth_price` AS `auth_price`,`gvec`.`exterior_img` AS `exterior_img`,`gvc`.`displacement` AS `displacement`,`gvc`.`logo` AS `logo`,`gvc`.`model_access_quantity` AS `model_access_quantity` from ((((((`gch_car_price` `gcp` left join `gch_view_interior_color` `gvic` on((`gcp`.`interior_color_id` = `gvic`.`id`))) left join `gch_view_exterior_color` `gvec` on((`gcp`.`exterior_color_id` = `gvec`.`id`))) left join `gch_view_car` `gvc` on((`gcp`.`car_id` = `gvc`.`id`))) join `gch_city` `gc`) left join `gch_province` `gp` on((`gc`.`province_id` = `gp`.`id`))) join `gch_user_4s` on(((`gch_user_4s`.`city_id` = `gc`.`id`) and (`gcp`.`user_id` = `gch_user_4s`.`id`)))) ;

-- ----------------------------
-- View structure for gch_view_exterior_color
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_exterior_color`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gch_view_exterior_color` AS select `gec`.`id` AS `id`,`gec`.`car_id` AS `car_id`,`gec`.`access_quantity` AS `access_quantity`,`gec`.`imgurl` AS `imgurl`,`gec`.`color_id` AS `color_id`,`gec`.`isdelete` AS `isdelete`,`gec`.`createtime` AS `createtime`,`gec`.`createuser` AS `createuser`,`gec`.`updatetime` AS `updatetime`,`gc`.`color_name` AS `color_name`,`gc`.`color_value` AS `color_value`,`gec`.`updateuser` AS `updateuser`,`gch_car`.`status` AS `status`,`gch_car_exterior_color_image`.`imgurl` AS `exterior_img` from (((`gch_exterior_color` `gec` left join `gch_color` `gc` on((`gec`.`color_id` = `gc`.`id`))) join `gch_car` on((`gec`.`car_id` = `gch_car`.`id`))) left join `gch_car_exterior_color_image` on((`gch_car_exterior_color_image`.`exterior_color_id` = `gec`.`id`))) ;

-- ----------------------------
-- View structure for gch_view_index_car_price
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_index_car_price`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `gch_view_index_car_price` AS select `gch_index_car_price`.`id` AS `id`,`gch_index_car_price`.`car_id` AS `car_id`,`gch_index_car_price`.`imgurl` AS `imgurl`,`gch_index_car_price`.`createtime` AS `createtime`,`gch_index_car_price`.`createuser` AS `createuser`,`gch_index_car_price`.`isdelete` AS `isdelete`,`gch_index_car_price`.`status` AS `status`,`gch_index_car_price`.`updatetime` AS `updatetime`,`gch_index_car_price`.`updateuser` AS `updateuser`,`gch_index_car_price`.`exterior_color_id` AS `exterior_color_id`,`gch_index_car_price`.`interior_color_id` AS `interior_color_id`,`gch_index_car_price`.`area_name` AS `area_name`,`gch_index_car_price`.`brand_alif` AS `brand_alif`,`gch_index_car_price`.`brand_id` AS `brand_id`,`gch_index_car_price`.`brand_name` AS `brand_name`,`gch_index_car_price`.`model_name` AS `car_model_name`,`gch_index_car_price`.`model_id` AS `car_model_id`,`gch_index_car_price`.`isbaojia` AS `isbaojia`,`gch_view_exterior_color`.`color_name` AS `exterior_color_name`,`gch_view_exterior_color`.`color_value` AS `exterior_color_value`,`gch_view_interior_color`.`color_name` AS `interior_color_name`,`gch_view_interior_color`.`color_value` AS `interior_color_value`,`gch_view_car`.`car_status` AS `car_status`,`gch_view_car`.`car_name` AS `car_name`,`gch_index_car_price`.`client` AS `client` from (((`gch_index_car_price` join `gch_view_exterior_color` on(((`gch_view_exterior_color`.`id` = `gch_index_car_price`.`exterior_color_id`) and ('' = '')))) left join `gch_view_interior_color` on((`gch_view_interior_color`.`id` = `gch_index_car_price`.`interior_color_id`))) left join `gch_view_car` on((`gch_view_car`.`id` = `gch_index_car_price`.`car_id`))) where (`gch_view_exterior_color`.`isdelete` = 0) ;

-- ----------------------------
-- View structure for gch_view_index_price
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_index_price`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gch_view_index_price` AS select `gch_index_car_price`.`id` AS `id`,`gch_index_car_price`.`car_id` AS `car_id`,`gch_index_car_price`.`createtime` AS `createtime`,`gch_index_car_price`.`createuser` AS `createuser`,`gch_index_car_price`.`isdelete` AS `isdelete`,`gch_index_car_price`.`status` AS `status`,`gch_index_car_price`.`updatetime` AS `updatetime`,`gch_index_car_price`.`updateuser` AS `updateuser`,`gch_index_car_price`.`interior_color_id` AS `interior_color_id`,`gch_index_car_price`.`area_name` AS `area_name`,`gch_index_car_price`.`brand_alif` AS `brand_alif`,`gch_index_car_price`.`brand_id` AS `brand_id`,`gch_index_car_price`.`brand_name` AS `brand_name`,`gch_index_car_price`.`model_name` AS `model_name`,`gch_index_car_price`.`model_id` AS `model_id`,`gch_index_car_price`.`isbaojia` AS `isbaojia`,`gch_index_car_price`.`exterior_color_id` AS `exterior_color_id`,`gch_view_car_price`.`user_id` AS `user_id`,`gch_view_car_price`.`exterior_color_name` AS `exterior_color_name`,`gch_view_car_price`.`exterior_color_value` AS `exterior_color_value`,`gch_view_car_price`.`interior_color_value` AS `interior_color_value`,`gch_view_car_price`.`interior_color_name` AS `interior_color_name`,`gch_view_car_price`.`exterior_color_imgurl` AS `exterior_color_imgurl`,`gch_view_car_price`.`auth_price` AS `auth_price`,`gch_view_car_price`.`city_id` AS `city_id`,`gch_view_car_price`.`city_name` AS `city_name`,`gch_view_car_price`.`province_id` AS `province_id`,`gch_view_car_price`.`province_name` AS `province_name`,`gch_view_car_price`.`car_type_id` AS `car_type_id`,`gch_view_car_price`.`car_type_name` AS `car_type_name`,`gch_view_car_price`.`car_name` AS `car_name`,`gch_index_car_price`.`imgurl` AS `imgurl`,`gch_view_car_price`.`displacement` AS `displacement`,`gch_view_car_price`.`car_model_imageurl` AS `car_model_imageurl`,`gch_view_car_price`.`price` AS `price`,`gch_view_car_price`.`discount` AS `discount`,`gch_view_car_price`.`low_price` AS `low_price`,`gch_view_car_price`.`car_status` AS `car_status`,`gch_view_car_price`.`logo` AS `logo`,`gch_view_car_price`.`exterior_img` AS `exterior_img`,`gch_view_car_price`.`is_xunjia` AS `is_xunjia`,`gch_index_car_price`.`client` AS `client`,`gch_view_car_price`.`transactions_count` AS `transactions_count` from (`gch_index_car_price` left join `gch_view_car_price` on(((`gch_index_car_price`.`exterior_color_id` = `gch_view_car_price`.`exterior_color_id`) and (`gch_index_car_price`.`interior_color_id` = `gch_view_car_price`.`interior_color_id`) and (`gch_index_car_price`.`isdelete` = 0) and (`gch_view_car_price`.`isdelete` = 0)))) ;

-- ----------------------------
-- View structure for gch_view_interior_color
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_interior_color`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gch_view_interior_color` AS select `gic`.`id` AS `id`,`gic`.`car_id` AS `car_id`,`gic`.`access_quantity` AS `access_quantity`,`gic`.`imgurl` AS `imgurl`,`gic`.`color_id` AS `color_id`,`gic`.`isdelete` AS `isdelete`,`gic`.`createtime` AS `createtime`,`gic`.`updatetime` AS `updatetime`,`gic`.`updateuser` AS `updateuser`,`gc`.`color_name` AS `color_name`,`gc`.`color_value` AS `color_value`,`gic`.`createuser` AS `createuser`,`gch_car`.`status` AS `status` from ((`gch_interior_color` `gic` left join `gch_color` `gc` on((`gic`.`color_id` = `gc`.`id`))) join `gch_car` on((`gic`.`car_id` = `gch_car`.`id`))) ;

-- ----------------------------
-- View structure for gch_view_receipt_address
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_receipt_address`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `gch_view_receipt_address` AS select `gch_receipt_address`.`id` AS `id`,`gch_receipt_address`.`user_id` AS `user_id`,`gch_receipt_address`.`receiver` AS `receiver`,`gch_receipt_address`.`telphone` AS `telphone`,`gch_receipt_address`.`receipt_address` AS `receipt_address`,`gch_receipt_address`.`status` AS `status`,`gch_receipt_address`.`createtime` AS `createtime`,`gch_receipt_address`.`createuser` AS `createuser`,`gch_receipt_address`.`isdelete` AS `isdelete`,`gch_receipt_address`.`updatetime` AS `updatetime`,`gch_receipt_address`.`updateuser` AS `updateuser`,`gch_data_province`.`ProvinceName` AS `provincename`,`gch_data_city`.`CityName` AS `cityname`,`gch_data_district`.`DistrictName` AS `districtname` from (((`gch_receipt_address` join `gch_data_city` on((`gch_receipt_address`.`receipt_city` = `gch_data_city`.`CityID`))) join `gch_data_district` on(((`gch_receipt_address`.`receipt_quarter` = `gch_data_district`.`DistrictID`) and (`gch_data_city`.`CityID` = `gch_data_district`.`CityID`)))) join `gch_data_province` on(((`gch_receipt_address`.`receipt_province` = `gch_data_province`.`ProvinceID`) and (`gch_data_province`.`ProvinceID` = `gch_data_city`.`ProvinceID`)))) ;

-- ----------------------------
-- View structure for gch_view_score_address
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_score_address`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `gch_view_score_address` AS select `gse`.`id` AS `id`,`gse`.`user_id` AS `user_id`,`gse`.`goods_id` AS `goods_id`,`gse`.`goods_name` AS `goods_name`,`gse`.`score_value` AS `score_value`,`gra`.`receiver` AS `receiver`,`gra`.`telphone` AS `telphone`,`gra`.`receipt_province` AS `receipt_province`,`gra`.`receipt_city` AS `receipt_city`,`gra`.`receipt_quarter` AS `receipt_quarter`,`gra`.`receipt_address` AS `receipt_address`,`gse`.`createtime` AS `createtime`,`gse`.`out_trade_no` AS `out_trade_no`,`gch_user_4s`.`user_name` AS `user_name`,`gch_user_4s`.`name_4s` AS `name_4s`,`gse`.`status` AS `status` from ((`gch_score_exchange` `gse` left join `gch_receipt_address` `gra` on(((`gse`.`user_id` = `gra`.`user_id`) and (`gse`.`address_id` = `gra`.`id`)))) join `gch_user_4s` on((`gra`.`user_id` = `gch_user_4s`.`id`))) where (`gse`.`user_id` = `gra`.`user_id`) ;

-- ----------------------------
-- View structure for gch_view_share_log
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_share_log`;
CREATE ALGORITHM=UNDEFINED DEFINER=`gouchehui`@`%` SQL SECURITY DEFINER VIEW `gch_view_share_log` AS select `gsl`.`id` AS `id`,`gug`.`user_name` AS `user_name`,`gug`.`tel` AS `tel`,concat(`gp`.`carstyle`,`gp`.`exterior_color_name`,`gp`.`interior_color_name`) AS `Name_exp_4`,`gug`.`activity_attention_num` AS `activity_attention_num`,`gug`.`activity_register_num` AS `activity_register_num`,`gug`.`buy_car_num` AS `buy_car_num` from ((`gch_share_logs` `gsl` join `gch_user_general` `gug`) join `gch_pay` `gp`) where ((`gsl`.`wx_open_id` = `gug`.`wx_open_id`) and (`gsl`.`pay_id` = `gp`.`id`)) ;

-- ----------------------------
-- View structure for gch_view_special_price_car
-- ----------------------------
DROP VIEW IF EXISTS `gch_view_special_price_car`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `gch_view_special_price_car` AS select `gspc`.`id` AS `id`,`gspc`.`user_id` AS `user_id`,`gspc`.`interior_color_id` AS `interior_color_id`,`gspc`.`exterior_color_id` AS `exterior_color_id`,`gspc`.`car_image` AS `car_image`,`gspc`.`car_id` AS `car_id`,`gspc`.`price` AS `price`,`gspc`.`special_price` AS `special_price`,`gspc`.`start_date` AS `start_date`,`gspc`.`end_date` AS `end_date`,`gspc`.`number` AS `number`,`gspc`.`description` AS `description`,`gspc`.`attention_count` AS `attention_count`,`gspc`.`isdelete` AS `isdelete`,`gvcc`.`exterior_color_name` AS `exterior_color_name`,`gvcc`.`car_name` AS `car_name`,`gvcc`.`brand_name` AS `brand_name`,`gvcc`.`car_model_name` AS `car_model_name`,`gvcc`.`brand_id` AS `brand_id`,`gvcc`.`car_model_id` AS `car_model_id`,`gvcc`.`interior_color_name` AS `interior_color_name`,`gvcc`.`car_status` AS `car_status`,`gvcc`.`brand_alif` AS `brand_alif`,`gvcc`.`auth_price` AS `auth_price`,`gc`.`city_name` AS `city_name`,`gc`.`province_id` AS `province_id`,`gu4`.`user_name` AS `user_name`,`gu4`.`name_4s` AS `name_4s`,`gu4`.`brand_4s` AS `brand_4s`,`gu4`.`city_id` AS `city_id`,`gp`.`province_name` AS `province_name`,`gvcc`.`exterior_img` AS `exterior_img`,`gspc`.`createtime` AS `createtime`,`gspc`.`createuser` AS `createuser`,`gspc`.`updateuser` AS `updateuser`,`gspc`.`updatetime` AS `updatetime`,`gvcc`.`exterior_color_value` AS `exterior_color_value`,`gvcc`.`interior_color_value` AS `interior_color_value`,`gspc`.`status` AS `status`,`gspc`.`remark` AS `remark`,`gvcc`.`displacement` AS `displacement`,(`gspc`.`price` - `gspc`.`special_price`) AS `discount`,`gspc`.`client` AS `client` from ((((`gch_special_price_car` `gspc` left join `gch_view_car_color` `gvcc` on(((`gvcc`.`car_id` = `gspc`.`car_id`) and (`gvcc`.`exterior_color_id` = `gspc`.`exterior_color_id`) and (`gvcc`.`interior_color_id` = `gspc`.`interior_color_id`)))) left join `gch_user_4s` `gu4` on((`gspc`.`user_id` = `gu4`.`id`))) left join `gch_city` `gc` on((`gu4`.`city_id` = `gc`.`id`))) left join `gch_province` `gp` on((`gc`.`province_id` = `gp`.`id`))) where (`gspc`.`isdelete` = 0) ;

-- ----------------------------
-- Procedure structure for asd
-- ----------------------------
DROP PROCEDURE IF EXISTS `asd`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `asd`()
BEGIN
	declare getid VARCHAR(32);
	declare car_num INT;
	declare stop int default 0;
	declare cur cursor for(SELECT id,month_car_num FROM gch_user WHERE role=2 and isdelete=0 and month_car_num>=10);
	 /*这把 游标 异常后 捕捉
		*并设置 循环使用 变量 stop 为 null 跳出循环。
		*/
	declare CONTINUE HANDLER FOR SQLSTATE '02000' SET stop = null;
	/*开游标*/
	OPEN cur;
	/*游标向下走一步，将查询出来的值付给定义的变量*/
		FETCH cur INTO getid,car_num;
		WHILE ( stop is not null) DO
				IF car_num>=30 THEN
				UPDATE gch_user SET total_jifen = total_jifen + 800,total_car_num = total_car_num + month_car_num WHERE id=getid;
				INSERT INTO `gch_score` (id, score, user_id, info)(SELECT REPLACE (uuid(), "-", ""),800,getid,'底价车次数不小于30次,积分奖励800' FROM gch_user WHERE id = getid);
				UPDATE gch_user SET month_car_num = 0 WHERE id=getid;
				UPDATE gch_user_car SET month_car_price_num_num = 0 WHERE user_id=getid;
			ELSE
				UPDATE gch_user SET total_jifen = total_jifen + 500,total_car_num = total_car_num + month_car_num WHERE id=getid;
				INSERT INTO `gch_score` (id, score, user_id, info)(SELECT REPLACE (uuid(), "-", ""),500,getid,'底价车次数不小于10次且不大于30次,积分奖励500' FROM gch_user WHERE id = getid);
				UPDATE gch_user SET month_car_num = 0 WHERE id=getid;
				UPDATE gch_user_car SET month_car_price_num_num = 0 WHERE user_id=getid;
			END IF;
		FETCH cur INTO getid,car_num;
		END WHILE;
	/*游标向下走一步*/
	CLOSE cur;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for nooffer_car_bybrand
-- ----------------------------
DROP PROCEDURE IF EXISTS `nooffer_car_bybrand`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `nooffer_car_bybrand`()
BEGIN

	DECLARE v_car_id VARCHAR(36); 
	DECLARE v_no_found INT DEFAULT 0; 	
	DECLARE cur_car_id CURSOR FOR  
		select DISTINCT a.id 
			from gch_car a,gch_car_model b 
			where  a.car_model_id=b.id 
			and b.brand_id='752' 
			and a.isdelete=0;
	
	DECLARE  CONTINUE HANDLER FOR NOT FOUND  SET  v_no_found = 1; /*when "not found" occur,just continue,这个是个条件处理,针对NOT FOUND的条件*/  
	

	/* for  loggging information 创建个临时表格来保持*/  
	CREATE TEMPORARY TABLE if not exists tmp_nooffer_car (  
		id int(11) NOT NULL AUTO_INCREMENT,  
		id1 varchar(32) ,  
		v1 varchar(32) ,  
		v2 varchar(32) ,  
		PRIMARY KEY (id)  
	 ); 

	truncate TABLE tmp_nooffer_car;

	start transaction;  
	
	OPEN  cur_car_id; /*Second: Open the cursor 接着使用OPEN打开游标*/  
	FETCH  cur_car_id INTO v_car_id; /*Third: now you can Fetch the row 把第一行数据写入变量中,游标也随之指向了记录的第一行*/  

	while v_no_found <> 1 do  
		insert into tmp_nooffer_car (id1,v1,v2)  select id,v1,v2 from (select id,v1,v2 from 
			(select a.car_id id,a.id v1,b.id v2 from gch_interior_color a  cross JOIN gch_exterior_color b on a.car_id=b.car_id ) aa
			where aa.id=v_car_id ) cc
			where CONCAT(v1,v2) not in 
			(
			select CONCAT(interior_color_id,exterior_color_id) from gch_view_car_price   where car_id=v_car_id
			);
		FETCH  cur_car_id INTO v_car_id;
	end WHILE;
/*
	REPEAT  
		insert into tmp_nooffer_car (id1,v1,v2)  select id,v1,v2 from (select id,v1,v2 from 
			(select a.car_id id,a.id v1,b.id v2 from gch_interior_color a  cross JOIN gch_exterior_color b on a.car_id=b.car_id ) aa
			where aa.id='86678d4bcb4cc6c31dcf4cd1b633e199' ) cc
			where CONCAT(v1,v2) not in 
			(
			select CONCAT(interior_color_id,exterior_color_id) from gch_view_car_price   where car_id='86678d4bcb4cc6c31dcf4cd1b633e199' 
			);
		
		FETCH  cur_car_id INTO v_car_id;
	UNTIL v_no_found=1 END REPEAT;  
*/
	CLOSE  cur_car_id;

	COMMIT;
		

	select * from tmp_nooffer_car;

END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for Score_Month_99_GetScoreMonthly
-- ----------------------------
DROP PROCEDURE IF EXISTS `Score_Month_99_GetScoreMonthly`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` PROCEDURE `Score_Month_99_GetScoreMonthly`()
BEGIN

/**
AUTHORS: 韩武洽
4S店每个月有10个不同的车款的报价被判定为底价获得积分500,超过300则获得积分800
执行时间: 

*/

# 常量-------START

-- 最低获得积分的标准
DECLARE CONS_LOWEST_SCORE_STANDARD INT DEFAULT 10;
-- 最低获得积分
DECLARE CONS_LOWEST_SCORE INT DEFAULT 500;

-- 倒数第二获得积分的标准
DECLARE CONS_PENULTIMATE_SCORE_STANDARD INT DEFAULT 30;
-- 倒数第二获得积分
DECLARE CONS_PENULTIMATE_SCORE INT DEFAULT 800;

# 常量-------END


# 变量-------START

-- 用来判断遍历游标是否执行完成
DECLARE v_done INT DEFAULT 0;
-- 定义循环给游标赋值
DECLARE v_userId,v_cnt VARCHAR (255) CHARACTER SET utf8;

# 变量-------START


-- 把sql的结果集赋值给priceList
DECLARE priceList CURSOR FOR 
SELECT
		price.user_id,
		COUNT(0) AS cnt
	FROM
		gch_car_price AS price
	WHERE
		isdelete = 0
	AND month_status = 1
	GROUP BY
		price.user_id
;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;
-- 关闭自动提交
SET AUTOCOMMIT = 0;

-- 先删除今天已经执行的"4S店每月不同底价车款获得不同积分"的记录
DELETE FROM gch_service WHERE service_num = 1 AND service_time = DATE(NOW());
-- 插入一条gch_service记录用来记录这条语句是否正确执行
INSERT INTO gch_service(service_num,service_name,service_time,start_time,service_describe,success_status)
VALUES(1,"4S店每月不同底价车款获得不同积分",DATE(NOW()),NOW(),"当月结束时执行,4S店有不同车款的报价被判定为底价时,获得不同的积分",1);



-- 开启循环
OPEN priceList;
-- 游标
REPEAT FETCH priceList INTO v_userId,v_cnt;

--  如果v_done=0时,则需要执行否则就不需要执行了
IF (v_done = 0) THEN
				IF    (v_cnt < CONS_PENULTIMATE_SCORE_STANDARD AND v_cnt >= CONS_LOWEST_SCORE_STANDARD) THEN

        -- 添加最低积分
        UPDATE gch_user_4s SET total_jifen = total_jifen + CONS_LOWEST_SCORE WHERE id = v_userId;
        -- 添加得分历史记录表
        INSERT INTO gch_score(id,user_id,score,info,createtime,isdelete)
        VALUES(REPLACE(UUID(),"-",""),v_userId,CONS_LOWEST_SCORE,"当月结束时执行,4S店有10-30种不同车款的报价被判定为底价,获得500的积分", NOW(),0);
  

				ELSEIF v_cnt >= CONS_PENULTIMATE_SCORE_STANDARD THEN 

        -- 添加最低积分
        UPDATE gch_user_4s SET total_jifen = total_jifen + CONS_PENULTIMATE_SCORE WHERE id = v_userId;

        -- 添加得分历史记录表
        INSERT INTO gch_score(id,user_id,score,info,createtime,isdelete)
        VALUES(REPLACE(UUID(),"-",""),v_userId,CONS_PENULTIMATE_SCORE,"当月结束时执行,4S店有超过30种不同车款的报价被判定为底价,获得800的积分",NOW(),0);

				END IF;

END IF;

UNTIL v_done = 1
END
REPEAT
;
-- 结束循环priceList
CLOSE priceList;

-- 不出现错误,则把添加的服务记录置为成功!
UPDATE gch_service SET end_time = NOW(),success_status = 9 WHERE service_num = 1 AND service_time = DATE(NOW());

COMMIT;


END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for Score_Quarter_1_4sGetScoreByFiveMinute
-- ----------------------------
DROP PROCEDURE IF EXISTS `Score_Quarter_1_4sGetScoreByFiveMinute`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` PROCEDURE `Score_Quarter_1_4sGetScoreByFiveMinute`()
BEGIN
	/*
AUTHORS: 韩武洽
说明: 积分: 判断车款成为底价超过20小时算一次,这里5分钟执行一次 查询是否到24小时

*/
-- 添加中间表 gch_user_car 用来计算季度多少底价车款
INSERT INTO gch_user_car 
(id,user_id,car_id,month_status,quarter_status,month_car_price_num,quarter_car_price_num,isdelete,createtime,updatetime)

-- 查询出成为底价
SELECT
	REPLACE(UUID(),"-","") AS id,
  price.user_id,
  price.car_id,
  "0",
  "1",
  "0",
  "1",
  "0",
  NOW(),
  NOW()
FROM
	gch_car_price AS price
WHERE price.quarter_status = 1
AND status_time >= SUBDATE(NOW(),INTERVAL 3 MINUTE)
AND status_time < SUBDATE(NOW(), INTERVAL - 2 MINUTE);


END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for Score_Quarter_2_4sGetScoreDaily
-- ----------------------------
DROP PROCEDURE IF EXISTS `Score_Quarter_2_4sGetScoreDaily`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` PROCEDURE `Score_Quarter_2_4sGetScoreDaily`()
BEGIN
# 每天23:58分执行一次函数
# 用来判断 车款成为底价被计分,价格不变仍为底价的条件下 每3天为一个单位 计算一次
INSERT INTO gch_user_car 
(id,user_id,car_id,month_status,quarter_status,month_car_price_num,quarter_car_price_num,isdelete,createtime,updatetime)

SELECT 
REPLACE(UUID(),"-","") AS id,
  price.user_id,
  price.car_id,
  "0",
  "1",
  "0",
  "1",
  "0",
  NOW(),
  NOW()

FROM gch_car_price AS price
# 判断不能为当天成为底价
WHERE DATEDIFF(NOW(),price.status_time) != 0
#判断成为底价和现在时间的间隔必须为3的倍数
AND DATEDIFF(NOW(),price.status_time) % 3 = 0
# 为每季度的最低价
AND price.quarter_status = 1
# 当前季度的数据例:1-3月 4-6月 7-9月 10-12月才有效
AND QUARTER(price.status_time)=QUARTER(now());

END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for Score_Quarter_99_4sReduceByQuarter
-- ----------------------------
DROP PROCEDURE IF EXISTS `Score_Quarter_99_4sReduceByQuarter`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` PROCEDURE `Score_Quarter_99_4sReduceByQuarter`()
BEGIN

/**
AUTHORS: 韩武洽
说明: 
		以季度为单位(1-3月 4-6月 7-9月 10-12月)车款成为底价的次数少于200次 扣除积分500
    次数计算方式:
				1. 车款报价成为低价超过24小时算一次数 (详情见存储过程 Score_Daily_4sGetScoreDaily)
        2. 车款报价成为底价被计分,价格不变仍为底价的条件下,每3天为一个单位,计算一次 (详情见存储过程 Score_Daily_4sGetScoreDaily)

执行时间:

*/


# 常量-------START

-- 季度 扣除积分的最低标准
DECLARE CONS_MINIMUM_LOWEST_CAR_STANDARD INT DEFAULT 200;

-- 最低扣除积分
DECLARE CONS_LOWEST_SCORE INT DEFAULT 500;

# 常量-------END



# 变量-------START

-- 用来判断遍历游标是否执行完成
DECLARE v_done INT DEFAULT 0;
-- 定义循环给游标赋值
DECLARE v_userId,v_cnt VARCHAR (255) CHARACTER SET utf8;

# 变量-------START


-- 把sql的结果集赋值给userCarList
DECLARE userCarList CURSOR FOR 
SELECT
	car.user_id,COUNT(0) AS cnt
FROM
	gch_user_car AS car
WHERE
	car.quarter_status = 1

GROUP BY car.user_id
HAVING cnt < CONS_MINIMUM_LOWEST_CAR_STANDARD;


DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;
-- 关闭自动提交
SET AUTOCOMMIT = 0;

-- 先删除今天已经执行的"4S店每季度底价车款少于200次,扣除500积分"的记录
DELETE FROM gch_service WHERE service_num = 2 AND service_time = DATE(NOW());
-- 插入一条gch_service记录用来记录这条语句是否正确执行
INSERT INTO gch_service(service_num,service_name,service_time,start_time,service_describe,success_status)
VALUES(2,"4s店每季度不达到底价车标准扣分",DATE(NOW()),NOW(),CONCAT("执行-->季度结束时,车款报价被判定为底价的次数少于",CONS_MINIMUM_LOWEST_CAR_STANDARD,"次,扣除",CONS_LOWEST_SCORE,"积分."),1);



-- 开启循环
OPEN userCarList;
-- 游标
REPEAT FETCH userCarList INTO v_userId,v_cnt;

--  如果v_done=0时,则需要执行否则就不需要执行了
IF (v_done = 0) THEN

		-- 减少当前用户XX 最低积分
		UPDATE gch_user_4s AS user4s SET user4s.total_jifen = IF(user4s.total_jifen > 500,user4s.total_jifen -500,0) WHERE user4s.id = v_userId;

		-- 添加得分历史记录表
		INSERT INTO gch_score(id,user_id,role,score,info,createtime,isdelete)
	  VALUES(REPLACE(UUID(),"-",""),v_userId,2,-CONS_LOWEST_SCORE,CONCAT("季度结束时,车款报价被判定为底价的次数少于",CONS_MINIMUM_LOWEST_CAR_STANDARD,"次,扣除",CONS_LOWEST_SCORE,"积分."), NOW(),0);
END IF;

UNTIL v_done = 1
END
REPEAT
;
-- 结束循环userCarList
CLOSE userCarList;

-- 不出现错误,则把添加的服务记录置为成功!
UPDATE gch_service SET end_time = NOW(),success_status = 9 WHERE service_num = 2 AND service_time = DATE(NOW());

COMMIT;


END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for ticket_pool
-- ----------------------------
DROP PROCEDURE IF EXISTS `ticket_pool`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `ticket_pool`(IN `ticket_id` int)
BEGIN
DECLARE i INT DEFAULT 1;
START TRANSACTION;
WHILE i<=500 DO
insert into gch_ticket_pool(ticket_id,ticket_number,ticket_name,money,validity,remark) SELECT id,CONCAT(ticket_prefix,date_format(NOW(),'%Y%m%d'),LPAD(i+release_amount, 8, 0)),ticket_name,money,validity,remark FROM gch_ticket WHERE isdelete=0 AND release_status=2 AND id=ticket_id;
SET i=i+1;
END WHILE;
COMMIT;
UPDATE gch_ticket SET release_amount=release_amount+500;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for ticket_user
-- ----------------------------
DROP PROCEDURE IF EXISTS `ticket_user`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `ticket_user`(IN `ticketid` int,IN `user_id` varchar(32),IN `fromticket` varchar(255),IN `activityid` int)
BEGIN
DECLARE v1 INT;
DECLARE v2 INT;
DECLARE v3 INT;
DECLARE i INT DEFAULT 1;
SET v1=(SELECT amount FROM gch_ticket WHERE id=ticketid);
#找出需要被处理的优惠券
SET v2=(SELECT id FROM gch_ticket_pool WHERE ticket_id=ticketid AND isdelete=0 ORDER BY id LIMIT 1);
SELECT v2;
START TRANSACTION;
#不限制数量的优惠券

IF v1=0 THEN
#券池里有这条数据
#IF ISNULL(v2) THEN
IF v2 is null THEN

WHILE i<=500 DO

insert into gch_ticket_pool(ticket_id,ticket_number,ticket_name,money,validity,remark) SELECT id,CONCAT(ticket_prefix,date_format(NOW(),'%Y%m%d'),LPAD(i+release_amount, 8, 0)),ticket_name,money,validity,remark FROM gch_ticket WHERE isdelete=0 AND release_status=2 AND id=ticketid;

SET i=i+1;

END WHILE;

UPDATE gch_ticket SET release_amount=(SELECT count(*) FROM gch_ticket_pool WHERE ticket_id=ticketid);

SET v3=(SELECT id FROM gch_ticket_pool WHERE ticket_id=ticketid AND isdelete=0 ORDER BY id LIMIT 1);
INSERT gch_ticket_user(user_id,ticket_id,ticket_number,ticket_name,money,validity,end_time,from_ticket,activity_id) SELECT 	user_id,ticket_id,ticket_number,ticket_name,money,validity,UNIX_TIMESTAMP(NOW()) + validity * 24 * 3600 AS end_time,fromticket AS from_ticket,activityid AS activity_id FROM gch_ticket_pool WHERE id=v3;
UPDATE gch_ticket_pool SET isdelete=1,updateuser=user_id,updatetime=NOW() WHERE id=v3;

#券池里没有数据，重新生成500条
ELSE

#添加一条进ticket_user,扣除ticke_pool表中的数据
INSERT gch_ticket_user(user_id,ticket_id,ticket_number,ticket_name,money,validity,end_time,from_ticket,activity_id) SELECT 	user_id,ticket_id,ticket_number,ticket_name,money,validity,UNIX_TIMESTAMP(NOW()) + validity * 24 * 3600 AS end_time,fromticket AS from_ticket,activityid AS activity_id  FROM gch_ticket_pool WHERE id=v2;
UPDATE gch_ticket_pool SET isdelete=1,updateuser=user_id,updatetime=NOW() WHERE id=v2;

END IF;

#限制数量的优惠券
ELSEIF v1>0 THEN

#添加一条进ticket_user,扣除ticke_pool表中的数据
INSERT gch_ticket_user(user_id,ticket_id,ticket_number,ticket_name,money,validity,end_time,from_ticket,activity_id) SELECT 	user_id,ticket_id,ticket_number,ticket_name,money,validity,UNIX_TIMESTAMP(NOW()) + validity * 24 * 3600 AS end_time,fromticket AS from_ticket,activityid AS activity_id  FROM gch_ticket_pool WHERE id=v2;
UPDATE gch_ticket_pool SET isdelete=1,updateuser=user_id,updatetime=NOW() WHERE id=v2;

END IF;
COMMIT;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for unquotes
-- ----------------------------
DROP PROCEDURE IF EXISTS `unquotes`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `unquotes`(IN `brand` varchar(32))
BEGIN
	#Routine body goes here...
START TRANSACTION;
SELECT brand_id,brand_name,car_model_name from gch_view_car_price WHERE brand_id=brand;
COMMIT;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for fun_car_count_for_car_id
-- ----------------------------
DROP FUNCTION IF EXISTS `fun_car_count_for_car_id`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` FUNCTION `fun_car_count_for_car_id`(`in_car_id` varchar(32)) RETURNS int(11)
    NO SQL
BEGIN

DECLARE o_count INT;

set o_count = (
	SELECT
		(
			ec.exterior_color_count * ic.interior_color_count
		) AS car_count
	FROM
		gch_car a
	LEFT JOIN (
		SELECT
			count(0) exterior_color_count,
			car_id
		FROM
			gch_exterior_color
		WHERE
			isdelete = 0
		GROUP BY
			car_id
	) AS ec ON ec.car_id = a.id
	LEFT JOIN (
		SELECT
			count(0) interior_color_count,
			car_id
		FROM
			gch_interior_color
		WHERE
			isdelete = 0
		GROUP BY
			car_id
	) AS ic ON ic.car_id = a.id
	WHERE
		a.id = in_car_id
	AND a.isdelete = 0
);

RETURN o_count;


END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for fun_car_count_for_car_model_id
-- ----------------------------
DROP FUNCTION IF EXISTS `fun_car_count_for_car_model_id`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` FUNCTION `fun_car_count_for_car_model_id`(`in_car_model_id` varchar(32)) RETURNS int(11)
    NO SQL
BEGIN
	#Routine body goes here...
#获取车型下全部车款总数 VARCHAR(500)
DECLARE o_count INT;

SET o_count = (
	SELECT
		SUM(car_count)
	FROM
		(
			SELECT
				a.car_model_id,
				(
					ec.exterior_color_count * ic.interior_color_count
				) AS car_count
			FROM
				gch_car a
			LEFT JOIN (
				SELECT
					count(0) exterior_color_count,
					car_id
				FROM
					gch_exterior_color
				WHERE
					isdelete = 0
				GROUP BY
					car_id
			) AS ec ON ec.car_id = a.id
			LEFT JOIN (
				SELECT
					count(0) interior_color_count,
					car_id
				FROM
					gch_interior_color
				WHERE
					isdelete = 0
				GROUP BY
					car_id
			) AS ic ON ic.car_id = a.id
			WHERE
				car_model_id = in_car_model_id
			AND a.isdelete = 0
		) car
);


IF (o_count is null) THEN

SET o_count = 0;


END
IF;

RETURN o_count;


END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for myFunction
-- ----------------------------
DROP FUNCTION IF EXISTS `myFunction`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `myFunction`() RETURNS varchar(32) CHARSET utf8
BEGIN
	declare aa varchar(32);
	declare bb varchar(32);
	SET aa=floor(100000 + RAND()*(999999-100000));
	SELECT my_num from `gch_user` where my_num= aa into bb;
	while bb is not null do 
		set aa=floor(100000 + RAND()*(999999-100000));
		SELECT my_num from `gch_user` where my_num= aa into bb;
	end while;

	return aa;
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for exchangetimes
-- ----------------------------
DROP EVENT IF EXISTS `exchangetimes`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `exchangetimes` ON SCHEDULE EVERY 1 MONTH STARTS '2016-09-01 00:15:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
UPDATE gch_user_4s SET exchange_usedtimes=0;
UPDATE gch_user_4s SET exchange_alltimes=3 WHERE total_jifen>10000;
UPDATE gch_user_4s SET exchange_alltimes=2 WHERE total_jifen>=5000 AND total_jifen<=10000;
UPDATE gch_user_4s SET exchange_alltimes=1 WHERE total_jifen<5000;
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for fs_score_30
-- ----------------------------
DROP EVENT IF EXISTS `fs_score_30`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `fs_score_30` ON SCHEDULE EVERY 1 MONTH STARTS '2016-04-01 00:30:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN

UPDATE gch_user_4s
SET total_jifen=total_jifen+(SELECT SUM(score)*0.3 FROM gch_score WHERE MONTH(createtime)=MONTH(NOW())-1 AND user_id=gch_user.id)
WHERE id IN (SELECT user_id from gch_score WHERE  MONTH(createtime)=MONTH(NOW())-1 GROUP BY user_id HAVING SUM(score)>3000);


INSERT INTO gch_score (id, user_id,role,score, info) SELECT
		REPLACE (uuid(), "-", ""),
		user_id,
     2,
		SUM(score) * 0.3,
CONCAT(MONTH (NOW())-1,'月份获得积分超过3000分，则返还当月积分的30%')
		
	FROM
		gch_score
	WHERE
		MONTH (createtime) = MONTH (NOW()) - 1
	GROUP BY
		user_id
	HAVING
		SUM(score) > 3000;

END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for fs_score_month
-- ----------------------------
DROP EVENT IF EXISTS `fs_score_month`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` EVENT `fs_score_month` ON SCHEDULE EVERY 1 MONTH STARTS '2016-04-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
	declare getid VARCHAR(32);
	declare car_num INT;
	declare stop int default 0;
	declare cur cursor for(SELECT id,month_car_num FROM gch_user WHERE role=2 and isdelete=0 and month_car_num>=10);
	 /*这把 游标 异常后 捕捉
		*并设置 循环使用 变量 stop 为 null 跳出循环。
		*/
	declare CONTINUE HANDLER FOR SQLSTATE '02000' SET stop = null;
	/*开游标*/
	OPEN cur;
	/*游标向下走一步，将查询出来的值付给定义的变量*/
		FETCH cur INTO getid,car_num;
		WHILE ( stop is not null) DO
				IF car_num>=30 THEN
				UPDATE gch_user_4s SET total_jifen = total_jifen + 800,total_car_num = total_car_num + month_car_num WHERE id=getid;
				INSERT INTO `gch_score` (id, score, user_id, role, info)(SELECT REPLACE (uuid(), "-", ""),800,getid,2,'底价车次数不小于30次,积分奖励800' FROM gch_user WHERE id = getid);
				UPDATE gch_user_4s SET month_car_num = 0 WHERE id=getid;
				UPDATE gch_user_car SET month_car_price_num_num = 0 WHERE user_id=getid;
			ELSE
				UPDATE gch_user_4s SET total_jifen = total_jifen + 500,total_car_num = total_car_num + month_car_num WHERE id=getid;
				INSERT INTO `gch_score` (id, score, user_id, role, info)(SELECT REPLACE (uuid(), "-", ""),500,getid,2,'底价车次数不小于10次且不大于30次,积分奖励500' FROM gch_user WHERE id = getid);
				UPDATE gch_user_4s SET month_car_num = 0 WHERE id=getid;
				UPDATE gch_user_car SET month_car_price_num_num = 0 WHERE user_id=getid;
			END IF;
		FETCH cur INTO getid,car_num;
		END WHILE;
	/*游标向下走一步*/
	CLOSE cur;
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for fs_score_quarter
-- ----------------------------
DROP EVENT IF EXISTS `fs_score_quarter`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` EVENT `fs_score_quarter` ON SCHEDULE EVERY 1 QUARTER STARTS '2016-04-01 01:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
#车款报价成为底价次数少于200，扣除500积分
INSERT INTO gch_score(id,user_id,role,score,info)
SELECT REPLACE(uuid(), "-", ""),id,2,-500,'季度车款报价成为底价次数少于200次' FROM gch_user_4s WHERE quarter_car_num<200;

UPDATE gch_user_4s
SET total_jifen=total_jifen-500
WHERE quarter_car_num<200 AND role=2;

#清空季度标志
UPDATE gch_car_price
SET quarter_status=0;

UPDATE gch_user_car
SET isdelete=1;


UPDATE gch_user_4s
SET quarter_car_num=0;
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for fs_score_three
-- ----------------------------
DROP EVENT IF EXISTS `fs_score_three`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `fs_score_three` ON SCHEDULE EVERY 3 DAY STARTS '2016-04-01 02:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
UPDATE gch_user_car
SET quarter_status=quarter_status+1
WHERE DATEDIFF(NOW(),updatetime)>=3 AND isdelete=0;
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for pay_effective
-- ----------------------------
DROP EVENT IF EXISTS `pay_effective`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `pay_effective` ON SCHEDULE EVERY 5 MINUTE STARTS '2016-07-05 13:05:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
#询价订单 支付之后两天失效
UPDATE gch_pay SET `status`=2 WHERE pay_obj=1 AND `status`=1 AND TIMESTAMPDIFF(HOUR,FROM_UNIXTIME(updatetime),NOW())>=48;
#定车订单 未支付后两天失效
UPDATE gch_pay SET `status`=2 WHERE pay_obj=2 AND type=1 AND `status`=0 AND TIMESTAMPDIFF(HOUR,createtime,NOW())>=48;
#定车订单失效 优惠券恢复(未过期)
UPDATE gch_ticket_user SET `status`=1,remark='订单失效，恢复优惠券' WHERE end_time>unix_timestamp(NOW()) AND isdelete=0 AND `status`=4 AND pay_id in (SELECT id FROM gch_pay WHERE `status`=2 AND pay_obj=2);
#定车订单失效 优惠券恢复(已过期)
UPDATE gch_ticket_user SET `status`=3,remark='订单失效，优惠券过期' WHERE end_time<=unix_timestamp(NOW()) AND isdelete=0 AND `status`=4 AND pay_id in (SELECT id FROM gch_pay WHERE `status`=2 AND pay_obj=2);
#优惠券过期失效
UPDATE gch_ticket_user SET `status`=3,remark='优惠券过期' WHERE end_time<=unix_timestamp(NOW()) AND isdelete=0 AND `status`=1;
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for Score_Month_99_Monthly
-- ----------------------------
DROP EVENT IF EXISTS `Score_Month_99_Monthly`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` EVENT `Score_Month_99_Monthly` ON SCHEDULE EVERY 1 MONTH STARTS '2016-04-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  CALL Score_Month_99_GetScoreMonthly();
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for Score_Quarter_1_FiveMonth
-- ----------------------------
DROP EVENT IF EXISTS `Score_Quarter_1_FiveMonth`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` EVENT `Score_Quarter_1_FiveMonth` ON SCHEDULE EVERY 5 MINUTE STARTS '2016-04-01 02:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
CALL Score_Quarter_1_4sGetScoreByFiveMinute();
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for Score_Quarter_2_Daily_Before
-- ----------------------------
DROP EVENT IF EXISTS `Score_Quarter_2_Daily_Before`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` EVENT `Score_Quarter_2_Daily_Before` ON SCHEDULE EVERY 1 DAY STARTS '2016-10-10 23:58:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
CALL Score_Quarter_2_4sGetScoreDaily();
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for Score_Quarter_99_After
-- ----------------------------
DROP EVENT IF EXISTS `Score_Quarter_99_After`;
DELIMITER ;;
CREATE DEFINER=`gouchehui`@`%` EVENT `Score_Quarter_99_After` ON SCHEDULE EVERY 1 QUARTER STARTS '2016-04-01 01:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
CALL Score_Quarter_99_4sReduceByQuarter();
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for special_price
-- ----------------------------
DROP EVENT IF EXISTS `special_price`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `special_price` ON SCHEDULE EVERY 5 MINUTE STARTS '2016-07-25 12:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
UPDATE gch_pay SET `status`=2 WHERE pay_obj=2 AND type=2 AND `status`=0 AND TIMESTAMPDIFF(MINUTE,createtime,NOW())>=30;
END
;;
DELIMITER ;

-- ----------------------------
-- Event structure for special_status
-- ----------------------------
DROP EVENT IF EXISTS `special_status`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` EVENT `special_status` ON SCHEDULE EVERY 5 MINUTE STARTS '2016-07-14 16:39:23' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
#未开始->在售
UPDATE gch_special_price_car SET `status`=3 WHERE `status`=2 AND TIMEDIFF(NOW(),start_date)>0 AND TIMEDIFF(NOW(),end_date)<0;
#未开始->过期
UPDATE gch_special_price_car SET `status`=5 WHERE `status`=2 AND TIMEDIFF(NOW(),end_date)>0;
#在售->过期
UPDATE gch_special_price_car SET `status`=5 WHERE `status`=3 AND TIMEDIFF(NOW(),end_date)>0;
#停售售->过期
UPDATE gch_special_price_car SET `status`=5 WHERE `status`=4 AND TIMEDIFF(NOW(),end_date)>0;
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `帖子的评论量增加`;
DELIMITER ;;
CREATE TRIGGER `帖子的评论量增加` AFTER INSERT ON `gch_car_life_comment` FOR EACH ROW begin
update gch_car_life_bbs set comment_amount=comment_amount+1 where new.pid=0 and id=new.bbs_id;
end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `点赞量增加`;
DELIMITER ;;
CREATE TRIGGER `点赞量增加` AFTER INSERT ON `gch_car_life_thumbs` FOR EACH ROW begin
if new.bbs_id !=''  then
update gch_car_life_bbs set thumbs_amount=thumbs_amount+1 where id=new.bbs_id;
elseif new.comment_id !='' then
update gch_car_life_comment set thumbs_amount=thumbs_amount+1 where id=new.comment_id;
end if;

end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `取消点赞`;
DELIMITER ;;
CREATE TRIGGER `取消点赞` AFTER UPDATE ON `gch_car_life_thumbs` FOR EACH ROW begin
if new.bbs_id !='' and new.isdelete=1 then
update gch_car_life_bbs set thumbs_amount=thumbs_amount-1 where id=new.bbs_id and thumbs_amount>0;
elseif new.comment_id !='' and new.isdelete=1 then
update gch_car_life_comment set thumbs_amount=thumbs_amount-1 where id=new.comment_id and thumbs_amount>0;
end if;

end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `insert`;
DELIMITER ;;
CREATE TRIGGER `insert` AFTER INSERT ON `gch_car_price` FOR EACH ROW BEGIN
#价格趋势图表
insert into gch_price_trend(car_price_id,exterior_color_id,interior_color_id,car_id,price,low_price,discount,createtime,createuser,createip,isdelete,updatetime,updateuser,updateip) values(new.id,new.exterior_color_id,new.interior_color_id,new.car_id,new.price,new.low_price,new.discount,new.createtime,new.createuser,new.createip,new.isdelete,new.updatetime,new.updateuser,new.updateip);
END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `update`;
DELIMITER ;;
CREATE TRIGGER `update` AFTER UPDATE ON `gch_car_price` FOR EACH ROW BEGIN

if old.low_price != new.low_price then
insert into gch_price_trend(car_price_id,exterior_color_id,interior_color_id,car_id,price,low_price,discount,createtime,createuser,createip,isdelete,updatetime,updateuser,updateip) values(new.id,new.exterior_color_id,new.interior_color_id,new.car_id,new.price,new.low_price,new.discount,new.updatetime,new.updateuser,new.createip,new.isdelete,new.updatetime,new.updateuser,new.updateip);
end if;

END
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `判别询价订单`;
DELIMITER ;;
CREATE TRIGGER `判别询价订单` BEFORE INSERT ON `gch_pay` FOR EACH ROW begin
DECLARE v1 varchar(32);
DECLARE tel varchar(32);
set v1=(SELECT cus_character FROM gch_pay WHERE user_id=new.user_id AND cus_character IS NOT NULL and pay_obj in(1,3) AND isdelete=0 ORDER BY createtime DESC  LIMIT 1);
if v1=2 then
set tel=(SELECT cus_name FROM gch_pay WHERE user_id=new.user_id AND cus_character=v1 and pay_obj in(1,3) AND isdelete=0 ORDER BY createtime DESC  LIMIT 1);
set new.cus_name=tel,new.cus_character=v1,new.cus_remark='系统自动判别',new.status_track=2 ;
end if;
end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `询价添加询价量订单添加订单量`;
DELIMITER ;;
CREATE TRIGGER `询价添加询价量订单添加订单量` BEFORE UPDATE ON `gch_pay` FOR EACH ROW begin
if new.status=1 then #支付成功
   if new.pay_obj=1 then  #询价
      #查询子订单中的底价车id进行逐个加1个
       update gch_car_price set asking_price_count = asking_price_count + 1 where id in (select  car_price_id from gch_pay_area_low_price where pay_id = old.id);
       #系统车型库添加询价次数
      update gch_car_model set xj_count = xj_count+ 1 where id in 
          (SELECT gc.car_model_id FROM gch_pay_area_low_price gpalp  LEFT JOIN gch_car_price gcp on gpalp.car_price_id = gcp.id  LEFT JOIN gch_car gc on gcp.car_id = gc.id  WHERE  gpalp.pay_id = old.id);
   end if;
   if new.pay_obj=2 then #订单
      update gch_car_price set transactions_count= transactions_count+ 1 where id = old.car_price_id;
   end if;
end if;
end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `底价车被询价一次增加20积分,定车按等级加分`;
DELIMITER ;;
CREATE TRIGGER `底价车被询价一次增加20积分,定车按等级加分` AFTER UPDATE ON `gch_pay` FOR EACH ROW begin

#询价积分
if new.pay_obj=1 and new.type=1 and new.status=1 and new.pay_way!='用户第一次免费询价' and new.pay_way!='微信端免费询价' then
#普通用户表中的总积分更新
update gch_user_general set total_jifen=total_jifen+100 where id=new.user_id;
#普通积分细则表
insert into gch_score(id,user_id,score,role,info,flag,from_id) values(REPLACE(uuid(), "-", ""),new.user_id,100,1,'询价一次加100积分',2,new.id);
#4s店用户表中的总积分更新
 update gch_user_4s set total_jifen=total_jifen+20 where id in (select id_4s from gch_pay_area_low_price where pay_id=new.id);
#积分细则表
  insert into gch_score(id,user_id,score,role,info,flag,from_id)  select REPLACE(uuid(), "-", ""),id_4s,20,2,'有一条底价车被询价增加20积分',2,new.id from gch_pay_area_low_price where pay_id=new.id ;
end if;

#定车积分
if new.pay_obj=2 and new.status=1 and new.is_distribute=0 then
#普通用户表中的总积分加1000
 update gch_user_general set total_jifen=total_jifen+1000 where id=new.user_id;
#普通用户积分细则表
insert into gch_score(id,user_id,role,score,info,flag,from_id) values(REPLACE(uuid(), "-", ""),new.user_id,1,1000,'定车一次，加1000积分',2,new.id);
if new.low_price<150000 then
#4s用户表中的总积分更新
 update gch_user_4s set total_jifen=total_jifen+300 where id=new.id_4s;
#积分细则表
  insert into gch_score(id,user_id,role,score,info,flag,from_id) values(REPLACE(uuid(), "-", ""),new.id_4s,2,300,'有一个15万以下的底价车订单增加300积分',2,new.id);
elseif new.low_price<300000 then
#用户表中的总积分更新
 update gch_user_4s set total_jifen=total_jifen+500 where id=new.id_4s;
#积分细则表
  insert into gch_score(id,user_id,role,score,info,flag,from_id) values(REPLACE(uuid(), "-", ""),new.id_4s,2,500,'有一个15-30万的底价车订单增加500积分',2,new.id);
else
#用户表中的总积分更新
 update gch_user_4s set total_jifen=total_jifen+800 where id=new.id_4s;
#积分细则表
  insert into gch_score(id,user_id,role,score,info,flag,from_id) values(REPLACE(uuid(), "-", ""),new.id_4s,2,800,'有一个30万以上的底价车订单增加800积分',2,new.id);
end if;
end if;

#特价车失效后 库存还原
if new.pay_obj=2 and new.status=2 and new.type=2 then
update gch_special_price_car set number=number+1 where id=new.car_special_id;
end if;

end
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `判别用户`;
DELIMITER ;;
CREATE TRIGGER `判别用户` BEFORE INSERT ON `gch_user_activity` FOR EACH ROW begin
DECLARE v1 varchar(32);
set v1=(SELECT cus_character FROM gch_user_activity WHERE tel=new.tel AND cus_character IS NOT NULL AND isdelete=0 ORDER BY createtime DESC  LIMIT 1);
if v1 is not null and v1!='' then
set new.cus_name=new.tel,new.cus_character=v1,new.cus_remark='系统自动判别',new.status_track=2 ;
end if;
set new.user_name=new.tel;
end
;;
DELIMITER ;
