/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operationdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2025-12-02 14:03:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_warehouse_class
-- ----------------------------
DROP TABLE IF EXISTS `opr_warehouse_class`;
CREATE TABLE `opr_warehouse_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL COMMENT '物品在倉庫的id',
  `class_str` varchar(100) DEFAULT NULL COMMENT '分类名称',
  `class_report` varchar(100) DEFAULT NULL COMMENT '清洁、灭虫、其它',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='倉庫物品类别表';
