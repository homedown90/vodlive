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

/*Data for the table `live_class` */

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
  `creator` int(11) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `modified_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `live_list` */

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

/*Data for the table `user` */

insert  into `user`(`id`,`account`,`email`,`password`,`salt`,`type`,`mi_account_id`,`create_time`,`modified_time`) values (1,'admin','homedown_90@163.com','e71811fd73aeebecdb47f3b330b9c569','cf67355a3333e6e143439161adc2d82e',1,NULL,'2017-12-27 06:13:58','2017-12-27 06:13:58');

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `vod_class` */

insert  into `vod_class`(`id`,`name`,`parent_id`,`is_leaf`,`path`,`create_time`,`modified_time`) values (1,'企业it部12',0,0,'0','2018-01-12 03:01:27','2018-01-12 09:12:55'),(2,'信息部',0,0,'0','2018-01-15 01:03:50','2018-01-24 09:47:32'),(3,'发生的发生',2,1,'0,2,','2018-01-15 01:04:12','2018-01-29 12:21:32'),(4,'2',1,1,'0,1,','2018-01-15 01:05:37','2018-01-29 12:21:36'),(5,'发送到发送到',1,1,'0,1,','2018-01-15 01:06:03','2018-01-29 12:21:38'),(6,'防守打法',2,1,'0,2,','2018-01-15 01:08:39','2018-01-29 12:21:40'),(7,'所发生的',2,1,'0,2,','2018-01-15 01:13:09','2018-01-29 12:21:41'),(8,'131',2,1,'0,2,','2018-01-15 01:14:49','2018-01-29 12:21:42'),(9,'&lt;script&gt;&lt;alert(&quot;sdfsd&quot;);/script&gt;',1,1,'0,1,','2018-01-15 01:20:57','2018-01-29 12:21:44'),(10,'测试',1,1,'0,1,','2018-01-15 02:12:34','2018-01-29 12:21:45'),(11,'dfdf ',2,1,'0,2,','2018-01-15 04:23:10','2018-01-29 12:21:52');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `vod_file` */

insert  into `vod_file`(`id`,`name`,`save_name`,`type`,`size`,`parent_id`,`sequence`,`js_md5`,`php_md5`,`is_upload`,`is_merge`,`is_hls`,`streams`) values (2,'6bfd85a4f63b471e798dbe37cf5892b8.mp4','20180205_0599f75f4eab197a21661ab87a4d31ec','mp4',13196928,0,0,'40c31642f71060b13fe8662e90a47848','',1,0,0,'');

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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;

/*Data for the table `vod_list` */

insert  into `vod_list`(`id`,`title`,`description`,`class_id`,`play_num`,`creator`,`create_time`,`modified_time`,`status`,`video_id`,`origin_url`,`picture_url`,`media_name`,`media_url`,`streams`,`to_hls`) values (13,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:09','2018-01-24 16:59:09',NULL,7,'','','',NULL,'',NULL),(14,'12','sdfsdf',4,NULL,1,'2018-01-24 16:59:09','2018-01-26 11:36:23',NULL,7,'','','',NULL,'',NULL),(15,'12','sdfsdf',5,NULL,1,'2018-01-24 16:59:09','2018-01-26 11:36:25',NULL,7,'','','',NULL,'',NULL),(16,'12','sdfsdf',6,NULL,1,'2018-01-24 16:59:10','2018-01-26 11:36:26',NULL,7,'','','',NULL,'',NULL),(17,'12','sdfsdf',7,NULL,1,'2018-01-24 16:59:10','2018-01-26 11:36:28',NULL,7,'','','',NULL,'',NULL),(18,'12','sdfsdf',8,NULL,1,'2018-01-24 16:59:10','2018-01-26 11:36:29',NULL,7,'','','',NULL,'',NULL),(19,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:10','2018-01-24 16:59:10',NULL,7,'','','',NULL,'',NULL),(20,'12','sdfsdf',4,NULL,1,'2018-01-24 16:59:10','2018-01-26 11:36:33',NULL,7,'','','',NULL,'',NULL),(21,'12','sdfsdf',5,NULL,1,'2018-01-24 16:59:11','2018-01-26 11:36:35',NULL,7,'','','',NULL,'',NULL),(22,'12','sdfsdf',6,NULL,1,'2018-01-24 16:59:11','2018-01-26 11:36:36',NULL,7,'','','',NULL,'',NULL),(23,'12','sdfsdf',7,NULL,1,'2018-01-24 16:59:11','2018-01-26 11:36:38',NULL,7,'','','',NULL,'',NULL),(24,'12','sdfsdf',8,NULL,1,'2018-01-24 16:59:11','2018-01-26 11:36:39',NULL,7,'','','',NULL,'',NULL),(25,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:11','2018-01-24 16:59:11',NULL,7,'','','',NULL,'',NULL),(27,'12','sdfsdf',5,NULL,1,'2018-01-24 16:59:12','2018-01-26 11:36:44',NULL,7,'','','',NULL,'',NULL),(28,'12','sdfsdf',6,NULL,1,'2018-01-24 16:59:12','2018-01-26 11:36:46',NULL,7,'','','',NULL,'',NULL),(29,'12','sdfsdf',7,NULL,1,'2018-01-24 16:59:12','2018-01-26 11:36:47',NULL,7,'','','',NULL,'',NULL),(30,'12','sdfsdf',8,NULL,1,'2018-01-24 16:59:12','2018-01-26 11:36:49',NULL,7,'','','',NULL,'',NULL),(31,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:12','2018-01-26 11:36:50',NULL,7,'','','',NULL,'',NULL),(32,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:12','2018-01-26 11:37:11',NULL,7,'','','',NULL,'',NULL),(33,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:13','2018-01-24 16:59:13',NULL,7,'','','',NULL,'',NULL),(34,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:13','2018-01-26 11:37:12',NULL,7,'','','',NULL,'',NULL),(35,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:13','2018-01-26 11:37:13',NULL,7,'','','',NULL,'',NULL),(36,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:13','2018-01-26 11:37:13',NULL,7,'','','',NULL,'',NULL),(37,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:13','2018-01-26 11:37:14',NULL,7,'','','',NULL,'',NULL),(38,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:13','2018-01-26 11:37:15',NULL,7,'','','',NULL,'',NULL),(39,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:14','2018-01-24 16:59:14',NULL,7,'','','',NULL,'',NULL),(40,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:14','2018-01-26 11:37:15',NULL,7,'','','',NULL,'',NULL),(41,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:14','2018-01-26 11:37:16',NULL,7,'','','',NULL,'',NULL),(42,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:14','2018-01-26 11:37:17',NULL,7,'','','',NULL,'',NULL),(43,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:14','2018-01-24 16:59:14',NULL,7,'','','',NULL,'',NULL),(44,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:14','2018-01-26 11:37:18',NULL,7,'','','',NULL,'',NULL),(45,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:15','2018-01-26 11:37:18',NULL,7,'','','',NULL,'',NULL),(46,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:15','2018-01-26 11:37:19',NULL,7,'','','',NULL,'',NULL),(47,'12','sdfsdf',9,NULL,1,'2018-01-24 16:59:15','2018-01-26 11:37:20',NULL,7,'','','',NULL,'',NULL),(48,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:15','2018-01-24 16:59:15',NULL,7,'','','',NULL,'',NULL),(49,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:15','2018-01-24 16:59:15',NULL,7,'','','',NULL,'',NULL),(50,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:15','2018-01-24 16:59:15',NULL,7,'','','',NULL,'',NULL),(51,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:16','2018-01-24 16:59:16',NULL,7,'','','',NULL,'',NULL),(52,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:16','2018-01-24 16:59:16',NULL,7,'','','',NULL,'',NULL),(53,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:16','2018-01-24 16:59:16',NULL,7,'','','',NULL,'',NULL),(54,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:16','2018-01-24 16:59:16',NULL,7,'','','',NULL,'',NULL),(55,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:16','2018-01-24 16:59:16',NULL,7,'','','',NULL,'',NULL),(56,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:16','2018-01-24 16:59:16',NULL,7,'','','',NULL,'',NULL),(57,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:17','2018-01-24 16:59:17',NULL,7,'','','',NULL,'',NULL),(58,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:17','2018-01-24 16:59:17',NULL,7,'','','',NULL,'',NULL),(59,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:17','2018-01-24 16:59:17',NULL,7,'','','',NULL,'',NULL),(60,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:17','2018-01-24 16:59:17',NULL,7,'','','',NULL,'',NULL),(61,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:17','2018-01-24 16:59:17',NULL,7,'','','',NULL,'',NULL),(62,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:17','2018-01-24 16:59:17',NULL,7,'','','',NULL,'',NULL),(63,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:18','2018-01-24 16:59:18',NULL,7,'','','',NULL,'',NULL),(64,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:18','2018-01-24 16:59:18',NULL,7,'','','',NULL,'',NULL),(65,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:18','2018-01-24 16:59:18',NULL,7,'','','',NULL,'',NULL),(66,'12','sdfsdf',3,NULL,1,'2018-01-24 16:59:18','2018-01-24 16:59:18',NULL,7,'','','',NULL,'',NULL),(67,'添加测试信息','测试成功',3,0,1,'2018-01-30 06:35:30','2018-01-30 08:10:33','closed',5,NULL,NULL,NULL,NULL,NULL,0),(68,'fsdf','sdfsdf',3,0,1,'2018-01-31 11:34:51','2018-01-31 11:34:51','closed',17,NULL,NULL,NULL,NULL,NULL,0);

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

/*Data for the table `vod_md5_file` */

insert  into `vod_md5_file`(`id`,`js_md5`,`file_name`,`server_md5`,`finish_upload`,`finish_merge`,`create_time`,`modified_time`) values (86,'acf5ce03247f3f36f78101a5895f2122','20180123111458_1bb5f95c119c70505655063a66c21266',NULL,NULL,NULL,'2018-01-23 11:14:58','2018-01-23 11:14:58'),(87,'652f4a4d0b1edcf0eac066da489e78e6','20180125021800_23f2151b06c01a2114d947aabee81b75',NULL,NULL,NULL,'2018-01-25 02:18:00','2018-01-25 02:18:00');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
