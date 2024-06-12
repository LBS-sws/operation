/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operationdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2024-06-12 15:33:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_order_goods_store
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_goods_store`;
CREATE TABLE `opr_order_goods_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_goods_id` int(100) NOT NULL COMMENT '领料物品id',
  `store_id` int(100) NOT NULL COMMENT '仓库id',
  `store_num` varchar(10) NOT NULL DEFAULT '0' COMMENT '发货数量',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='技术员领料的发货需要指定仓库';
