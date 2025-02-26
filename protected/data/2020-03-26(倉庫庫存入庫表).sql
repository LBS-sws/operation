/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-03-26 14:16:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_storage
-- ----------------------------
DROP TABLE IF EXISTS `opr_storage`;
CREATE TABLE `opr_storage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL COMMENT '入庫編號',
  `apply_time` date NOT NULL COMMENT '入庫時間',
  `remark` text COMMENT '入庫備註',
  `city` varchar(255) DEFAULT NULL,
  `status_type` int(11) NOT NULL DEFAULT '0' COMMENT '0:草稿  1：不可修改',
  `storage_code` varchar(255) DEFAULT NULL,
  `storage_name` text,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='倉庫的入庫單（只能修改倉庫的物品數量）';

-- ----------------------------
-- Table structure for opr_storage_info
-- ----------------------------
DROP TABLE IF EXISTS `opr_storage_info`;
CREATE TABLE `opr_storage_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `storage_id` int(11) NOT NULL COMMENT '入庫單id',
  `warehouse_id` int(11) NOT NULL COMMENT '倉庫物品id',
  `add_num` float(11,4) NOT NULL COMMENT '增加的數量',
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8;
