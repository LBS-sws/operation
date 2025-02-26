/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operationdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2022-03-28 16:17:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_service_deduct
-- ----------------------------
DROP TABLE IF EXISTS `opr_service_deduct`;
CREATE TABLE `opr_service_deduct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `service_code` varchar(255) DEFAULT NULL COMMENT '扣分編號',
  `deduct_date` date NOT NULL COMMENT '扣分日期',
  `deduct_type` int(8) NOT NULL DEFAULT '1' COMMENT '扣分類型 1：警告信 2：产生赔偿的客诉',
  `service_year` int(4) NOT NULL,
  `service_month` int(2) NOT NULL,
  `score_num` int(11) NOT NULL DEFAULT '0' COMMENT '分數',
  `remark` text COMMENT '備註',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='技術員扣分';

-- ----------------------------
-- Table structure for opr_service_money
-- ----------------------------
DROP TABLE IF EXISTS `opr_service_money`;
CREATE TABLE `opr_service_money` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `service_code` varchar(255) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `service_year` int(4) NOT NULL,
  `service_month` int(2) NOT NULL,
  `service_money` float(11,2) NOT NULL DEFAULT '0.00' COMMENT '服務金額',
  `score_num` float(11,2) NOT NULL DEFAULT '0.00' COMMENT '得分',
  `remark` text COMMENT '備註',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='技術員服务金额表';

-- ----------------------------
-- Table structure for opr_service_new
-- ----------------------------
DROP TABLE IF EXISTS `opr_service_new`;
CREATE TABLE `opr_service_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `service_code` varchar(255) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `service_year` int(4) NOT NULL,
  `service_month` int(2) NOT NULL,
  `service_num` int(8) NOT NULL DEFAULT '1' COMMENT '服務單數',
  `score_num` int(11) NOT NULL DEFAULT '0',
  `remark` text COMMENT '備註',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='技術員介紹新生意表';

-- ----------------------------
-- Table structure for opr_technician_rank
-- ----------------------------
DROP TABLE IF EXISTS `opr_technician_rank`;
CREATE TABLE `opr_technician_rank` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rank_num` int(11) NOT NULL DEFAULT '0' COMMENT '排行名次',
  `rank_year` int(4) NOT NULL,
  `rank_month` int(2) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `integral_num` int(8) DEFAULT NULL COMMENT '學分',
  `charity_num` int(8) DEFAULT NULL COMMENT '慈善分',
  `pin_num` int(8) DEFAULT NULL COMMENT '襟章得分',
  `service_num` float(10,2) DEFAULT NULL COMMENT '服務得分',
  `prize_num` int(8) DEFAULT NULL COMMENT '表揚信得分',
  `complain_num` int(8) DEFAULT NULL COMMENT '客诉跟进得分',
  `quality_num` float(10,2) DEFAULT NULL COMMENT '質檢得分',
  `review_num` int(8) DEFAULT NULL COMMENT '考核得分',
  `letter_num` int(8) DEFAULT NULL COMMENT '心意信得分',
  `recommend_num` int(8) DEFAULT NULL COMMENT '推薦人得分',
  `two_num` int(8) DEFAULT NULL COMMENT '兩項得分',
  `new_num` int(8) DEFAULT NULL COMMENT '介绍新生意得分',
  `sales_num` int(8) DEFAULT NULL COMMENT '洗地易销售得分',
  `deduct_num` int(8) DEFAULT NULL COMMENT '扣分分數',
  `score_sum` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '總分',
  `other_score` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '不包含特殊分數的總分',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='每月排行榜';
