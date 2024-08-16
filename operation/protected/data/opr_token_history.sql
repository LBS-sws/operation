/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operationdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2024-08-16 14:49:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_token_history
-- ----------------------------
DROP TABLE IF EXISTS `opr_token_history`;
CREATE TABLE `opr_token_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) NOT NULL,
  `token_type` varchar(255) NOT NULL,
  `token_json` text,
  `lcu` varchar(255) DEFAULT NULL,
  `lcd` datetime DEFAULT NULL,
  `lud` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
