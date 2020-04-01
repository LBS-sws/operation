/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-04-01 16:01:10
*/
-- ----------------------------
-- Table structure for opr_storage_info
-- ----------------------------
alter table opr_storage_info add supplier_id int(11) NULL DEFAULT NULL COMMENT '供應商id' after warehouse_id;
