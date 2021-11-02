/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-11-01 17:01:10
*/
-- ----------------------------
-- Table structure for opr_warehouse_price
-- ----------------------------
alter table opr_warehouse_price add new_num int(11) NOT NULL DEFAULT 1 COMMENT '是否新导入的价格 0：不是  1：是' after price;

-- ----------------------------
-- Table structure for opr_order_goods
-- ----------------------------
alter table opr_order_goods add total_price decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '物品总价'  after etd;

-- ----------------------------
-- Table structure for opr_order
-- ----------------------------
alter table opr_order add total_price decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT '订单总价' after audit_time;
