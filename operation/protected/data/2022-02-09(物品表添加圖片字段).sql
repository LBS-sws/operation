/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2022-02-09 12:01:10
*/
-- ----------------------------
-- Table structure for opr_goods_do
-- ----------------------------
alter table opr_goods_do add img_url varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '圖片地址' after stickies_id;

-- ----------------------------
-- Table structure for opr_goods_fa
-- ----------------------------
alter table opr_goods_fa add img_url varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '圖片地址' after origin;

-- ----------------------------
-- Table structure for opr_goods_im
-- ----------------------------
alter table opr_goods_im add img_url varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '圖片地址' after inspection;

