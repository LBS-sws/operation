/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-01-08 17:04:43
*/

-- ----------------------------
-- Table structure for opr_warehouse_price
-- ----------------------------
CREATE TABLE `opr_warehouse_price` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL COMMENT '物品在倉庫的id',
  `year` int(11) NOT NULL DEFAULT '2020',
  `month` int(11) NOT NULL DEFAULT '1',
  `price` float(10,2) NOT NULL DEFAULT '0.00',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='倉庫物品單價表';


-- ----------------------------
-- Table structure for opr_order
-- ----------------------------
ALTER TABLE opr_order ADD COLUMN `total_price`  float NULL DEFAULT 0 COMMENT '訂單總價' AFTER audit_time;