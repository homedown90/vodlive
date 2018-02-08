/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 5.6.16 : Database - mi_vod_live
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mi_vod_live` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `mi_vod_live`;

/*Table structure for table `live_class` */

DROP TABLE IF EXISTS `live_class`;

CREATE TABLE `live_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '类别自增id',
  `name` varchar(255) NOT NULL COMMENT '类别名称',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父id',
  `path` varchar(255) DEFAULT '' COMMENT '节点路径',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `modified_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `live_list` */

DROP TABLE IF EXISTS `live_list`;

CREATE TABLE `live_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '直播自增id',
  `title` varchar(255) NOT NULL COMMENT '直播标题',
  `description` text NOT NULL COMMENT '直播简介',
  `online_num` int(11) unsigned zerofill NOT NULL DEFAULT '00000000000' COMMENT '在线人数',
  `reserve_num` int(11) unsigned zerofill NOT NULL DEFAULT '00000000000' COMMENT '预约人数',
  `status` enum('closed','draft','published') DEFAULT NULL COMMENT '发布状态：关闭、未发布、发布',
  `picture_url` varchar(255) DEFAULT '' COMMENT '展示图片路径',
  `streams` varchar(255) DEFAULT '' COMMENT '播放流地址',
  `salt` varchar(32) DEFAULT NULL COMMENT '视频播放掩码',
  `key` varchar(32) DEFAULT NULL COMMENT '临时播放key',
  `creator` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `modified_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `live_order` */

DROP TABLE IF EXISTS `live_order`;

CREATE TABLE `live_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `live_id` int(11) NOT NULL COMMENT '预约视频id',
  `is_order` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否预约',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `modified_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `account` varchar(128) NOT NULL COMMENT '用户账号',
  `email` varchar(128) NOT NULL COMMENT '邮件',
  `password` varchar(64) NOT NULL COMMENT '用户密码',
  `salt` varchar(32) NOT NULL COMMENT '密码salt',
  `type` smallint(6) NOT NULL DEFAULT '2' COMMENT '1:admin,2:customer',
  `mi_account_id` int(11) DEFAULT NULL COMMENT '小米账号id',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `modified_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

/*Table structure for table `vod_class` */

DROP TABLE IF EXISTS `vod_class`;

CREATE TABLE `vod_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '类别自增id',
  `name` varchar(255) NOT NULL COMMENT '类别名称',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父id',
  `is_leaf` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为父节点',
  `path` varchar(255) DEFAULT '' COMMENT '节点路径',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `modified_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Table structure for table `vod_file` */

DROP TABLE IF EXISTS `vod_file`;

CREATE TABLE `vod_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '上传文件id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名称',
  `save_name` varchar(255) NOT NULL DEFAULT '' COMMENT '保存文件名称',
  `type` enum('mp4') NOT NULL DEFAULT 'mp4' COMMENT '文件类型',
  `size` bigint(20) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父文件id',
  `sequence` smallint(6) NOT NULL DEFAULT '0' COMMENT '切片的文件序列号',
  `js_md5` varchar(64) NOT NULL DEFAULT '' COMMENT '前端上传时md5',
  `php_md5` varchar(64) DEFAULT '' COMMENT '后端合并时md5',
  `is_upload` tinyint(1) DEFAULT '0' COMMENT '是否上传完成',
  `is_merge` tinyint(1) DEFAULT '0' COMMENT '是否合并完成',
  `is_hls` tinyint(1) DEFAULT '0' COMMENT '是否切流成功',
  `streams` varchar(255) DEFAULT '' COMMENT '流媒体路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Table structure for table `vod_list` */

DROP TABLE IF EXISTS `vod_list`;

CREATE TABLE `vod_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '点播自增id',
  `title` varchar(255) NOT NULL COMMENT '点播标题',
  `description` text NOT NULL COMMENT '点播简介',
  `class_id` int(11) NOT NULL COMMENT '点播分类',
  `play_num` int(10) unsigned DEFAULT '0' COMMENT '点播次数',
  `creator` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `modified_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `status` enum('closed','draft','published') DEFAULT 'closed' COMMENT '发布状态：关闭、未发布、发布',
  `video_id` int(11) DEFAULT NULL COMMENT '视频文件唯一标识',
  `origin_url` varchar(255) DEFAULT '' COMMENT '视频保存地址',
  `picture_url` varchar(255) DEFAULT '' COMMENT '展示图片路径',
  `media_name` varchar(255) DEFAULT '' COMMENT '上传媒体的名称',
  `media_url` varchar(255) DEFAULT NULL COMMENT '上传媒体的路径',
  `streams` varchar(255) DEFAULT '' COMMENT '播放流地址',
  `to_hls` tinyint(1) DEFAULT '0' COMMENT '是否切片成功',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

/*Table structure for table `vod_md5_file` */

DROP TABLE IF EXISTS `vod_md5_file`;

CREATE TABLE `vod_md5_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `js_md5` varchar(64) NOT NULL COMMENT '前端md5序列号',
  `file_name` varchar(255) NOT NULL COMMENT '文件名称',
  `server_md5` varchar(64) DEFAULT '' COMMENT '服务器md5序列',
  `finish_upload` tinyint(1) DEFAULT '0' COMMENT '是否上传完成',
  `finish_merge` tinyint(1) DEFAULT '0' COMMENT '是否合并完成',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `modified_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
