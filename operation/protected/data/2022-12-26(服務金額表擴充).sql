/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2022-12-26 15:01:10
*/
-- ----------------------------
-- Table structure for opr_service_money
-- ----------------------------
alter table opr_service_money add night_money float(11,2) NOT NULL DEFAULT 0.00 COMMENT '夜单金额' after score_num;
alter table opr_service_money add night_score float(11,2) NOT NULL DEFAULT 0.00 COMMENT '夜单得分'  after score_num;
alter table opr_service_money add create_money float(11,2) NOT NULL DEFAULT 0.00 COMMENT '创新服务金额' after score_num;
alter table opr_service_money add create_score float(11,2) NOT NULL DEFAULT 0.00 COMMENT '创新服务得分' after score_num;
alter table opr_service_money add update_u int(1) NOT NULL DEFAULT 1 COMMENT 'u系统自动同步 1：同步 0：不同步' after score_num;

-- ----------------------------
-- Table structure for opr_technician_rank
-- ----------------------------
alter table opr_technician_rank add night_num float(11,2) NULL DEFAULT 0.00 COMMENT '夜单得分' after score_num;
alter table opr_technician_rank add create_num float(11,2) NULL DEFAULT 0.00 COMMENT '创新服务得分'  after score_num;
