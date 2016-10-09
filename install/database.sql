
USE microweekend;

-- 用户表
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) NOT NULL,
  `nickname` varchar(20) DEFAULT NULL,
  `realname` varchar(20) DEFAULT NULL COMMENT '真实姓名',
  `password` varchar(32) NOT NULL,
  `user_auth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户认证',
  `user_gender` varchar(2) DEFAULT NULL COMMENT '性别',
  `display_pic` int(10) COMMENT '用户头像图片地址',
  `uuid` varchar(50) COMMENT '用户的uuid',
  `create_time` datetime DEFAULT NULL ,
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `password` (`password`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 发布内容表
--
DROP TABLE IF EXISTS `content`;
CREATE TABLE IF NOT EXISTS `content` (
  `content_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `content_title` varchar(50) NOT NULL,
  `content_body` text NOT NULL,
  `content_time` varchar(50) NOT NULL,
  `content_address` varchar(50) NOT NULL,
  `latitude` double DEFAULT '0.0' COMMENT '位置的经度',
  `longitude` double DEFAULT '0.0' COMMENT '位置的纬度',
  `index_pic` int(10) NOT NULL,
  `charge_type` SET(  'a',  'f',  'p' ) NOT NULL DEFAULT 'f' COMMENT 'a-AA,f-free,p-pay',
  `charge` int(10) NOT NULL DEFAULT '0',
  `create_time` datetime DEFAULT NULL ,
  PRIMARY KEY (`content_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `content_title` (`content_title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 存储图片信息
DROP TABLE IF EXISTS `picture`;
CREATE TABLE IF NOT EXISTS `picture` (
  `pic_id` int(11) NOT NULL AUTO_INCREMENT,
  `pic_uses` int(10) NOT NULL default '0' COMMENT '图片被使用次数',
  `is_displaypic` int(1) DEFAULT '0' COMMENT '是否是用户头像',
  `pic_name` varchar(50) NOT NULL,
  `pic_path` varchar(50) NOT NULL,
  `create_time` datetime DEFAULT NULL ,
  PRIMARY KEY (`pic_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- 活动订单
DROP TABLE IF EXISTS `mkorder`;
CREATE TABLE IF NOT EXISTS `mkorder` (
  `order_id` int(10) NOT NULL AUTO_INCREMENT,
  `content_id` int(10) NOT NULL COMMENT '活动id',
  `user_id` int(10) NOT NULL COMMENT '参加活动的用户',
  `status` tinyint(1) DEFAULT '1' ,
  `pay_status` tinyint(1) DEFAULT '0' ,
  `create_time` datetime DEFAULT NULL ,
  `is_del` tinyint(1) DEFAULT '0' ,
  PRIMARY KEY (`order_id`),
  KEY `content_id` (`content_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;




