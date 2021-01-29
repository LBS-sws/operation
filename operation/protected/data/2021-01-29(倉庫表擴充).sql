/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-01-29 17:01:10
*/
-- ----------------------------
-- Table structure for opr_warehouse
-- ----------------------------
alter table opr_warehouse add display int(2) NOT NULL DEFAULT 1 COMMENT '是否顯示 1：顯示  0：不顯示' after z_index;
