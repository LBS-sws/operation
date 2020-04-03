/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-04-03 18:22:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_warehouse_back
-- ----------------------------
DROP TABLE IF EXISTS `opr_warehouse_back`;
CREATE TABLE `opr_warehouse_back` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `back_num` float(11,4) DEFAULT NULL COMMENT '退回數量',
  `old_num` float(11,4) DEFAULT NULL COMMENT '退回前數量',
  `lcu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COMMENT='倉庫訂單退回物品表';
