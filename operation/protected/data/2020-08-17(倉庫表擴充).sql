/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-08-17 15:01:10
*/
-- ----------------------------
-- Table structure for opr_warehouse
-- ----------------------------
alter table opr_warehouse add matching text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '產品配比' after min_num;
alter table opr_warehouse add matters text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '注意事項' after matching;
