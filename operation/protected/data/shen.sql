/*
Navicat MySQL Data Transfer

Source Server         : vm1
Source Server Version : 50626
Source Host           : 192.168.1.6:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2017-06-17 10:57:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_goods
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods`;
CREATE TABLE `opr_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '物品名字',
  `type` varchar(30) NOT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` varchar(30) NOT NULL COMMENT '物品單價',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Records of opr_goods
-- ----------------------------
INSERT INTO `opr_goods` VALUES ('3', '肥皂液-花香味（黑色）', '3桶/箱', '桶', '16.05', null, null, '2017-06-13 16:11:03', '2017-06-13 16:11:03');
INSERT INTO `opr_goods` VALUES ('4', '肥皂液-花香味（紫色）', '2桶/箱', '桶', '14.05', null, null, '2017-06-13 16:06:17', '2017-06-13 16:06:17');
INSERT INTO `opr_goods` VALUES ('5', '肥皂液-花香味（無色）', '7桶/箱', '桶', '8.05', '', '', '2017-06-15 17:12:16', '2017-06-15 17:12:16');
INSERT INTO `opr_goods` VALUES ('6', '肥皂液-花香味（紅色）', '4桶/箱', '桶', '11.05', '', '', '2017-06-15 17:12:10', '2017-06-15 17:12:10');
INSERT INTO `opr_goods` VALUES ('7', '肥皂液-花香味（黃色）', '5桶/箱', '桶', '21.05', '', '', '2017-06-15 17:12:10', '2017-06-15 17:12:10');
INSERT INTO `opr_goods` VALUES ('8', '肥皂液-花香味（藍色）', '1桶/箱', '桶', '41.05', '', '', '2017-06-15 17:12:10', '2017-06-15 17:12:10');
INSERT INTO `opr_goods` VALUES ('9', '肥皂液-花香味（橙色）', '3桶/箱', '桶', '12', '', '', '2017-06-15 17:12:10', '2017-06-15 17:12:10');

-- ----------------------------
-- Table structure for opr_order
-- ----------------------------
DROP TABLE IF EXISTS `opr_order`;
CREATE TABLE `opr_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '訂單id',
  `order_code` varchar(10) DEFAULT NULL COMMENT '訂單號（自動生成）',
  `order_user` varchar(30) NOT NULL COMMENT '訂購的用戶',
  `technician` varchar(10) DEFAULT NULL COMMENT '技術員',
  `status` varchar(30) NOT NULL COMMENT '訂單狀態（pending / sent / approve / reject / cancelled）',
  `remark` varchar(255) DEFAULT NULL COMMENT '備註',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='訂單表';

-- ----------------------------
-- Records of opr_order
-- ----------------------------
INSERT INTO `opr_order` VALUES ('9', '00009', 'test', 'test', 'pending', '物品少算了一個，重新加了一個貨物', 'test', 'test', '2017-06-17 10:43:27', '2017-06-17 10:43:29');
INSERT INTO `opr_order` VALUES ('10', '00010', 'test', '', 'pending', null, 'test', null, '2017-06-17 10:35:22', '2017-06-17 10:35:22');
INSERT INTO `opr_order` VALUES ('11', '00011', 'test', 'shenchao', 'sent', '指定技術員，而且發送', 'test', 'test', '2017-06-17 10:37:41', '2017-06-17 10:37:42');
INSERT INTO `opr_order` VALUES ('12', '00012', 'test', 'test', 'approve', '確定了數量，可以發貨', 'test', 'test', '2017-06-17 10:52:47', '2017-06-17 10:52:48');

-- ----------------------------
-- Table structure for opr_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_goods`;
CREATE TABLE `opr_order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL COMMENT '物品id',
  `order_id` int(10) unsigned NOT NULL COMMENT '訂單id',
  `goods_num` int(10) NOT NULL COMMENT '物品數量',
  `confirm_num` int(10) DEFAULT NULL COMMENT '實際數量',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='訂單內的物品表';

-- ----------------------------
-- Records of opr_order_goods
-- ----------------------------
INSERT INTO `opr_order_goods` VALUES ('21', '6', '9', '4', null, 'test', 'test', '2017-06-17 10:43:27', '2017-06-17 10:43:29');
INSERT INTO `opr_order_goods` VALUES ('22', '7', '9', '1', null, 'test', 'test', '2017-06-17 10:43:27', '2017-06-17 10:43:29');
INSERT INTO `opr_order_goods` VALUES ('23', '3', '10', '1', null, 'test', null, '2017-06-17 10:35:23', null);
INSERT INTO `opr_order_goods` VALUES ('24', '5', '11', '1', null, 'test', 'test', '2017-06-17 10:37:41', '2017-06-17 10:37:42');
INSERT INTO `opr_order_goods` VALUES ('25', '9', '11', '2', null, 'test', 'test', '2017-06-17 10:37:41', '2017-06-17 10:37:42');
INSERT INTO `opr_order_goods` VALUES ('26', '5', '9', '3', null, 'test', null, '2017-06-17 10:43:29', null);
INSERT INTO `opr_order_goods` VALUES ('27', '5', '12', '4', '3', 'test', 'test', '2017-06-17 10:52:47', '2017-06-17 10:52:48');
INSERT INTO `opr_order_goods` VALUES ('28', '6', '12', '1', '1', 'test', 'test', '2017-06-17 10:52:47', '2017-06-17 10:52:48');

-- ----------------------------
-- Table structure for opr_order_status
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_status`;
CREATE TABLE `opr_order_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '訂單狀態id',
  `order_id` int(10) unsigned NOT NULL,
  `status` varchar(30) NOT NULL COMMENT '訂單狀態（pending / sent / approve / reject / cancelled）',
  `r_remark` varchar(255) DEFAULT NULL COMMENT '備註',
  `time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '訂單狀態時間',
  `lcu` varchar(255) NOT NULL COMMENT '操作人員',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='訂單的狀態表';

-- ----------------------------
-- Records of opr_order_status
-- ----------------------------
INSERT INTO `opr_order_status` VALUES ('28', '9', 'pending', null, '2017-06-17 10:34:40', 'test');
INSERT INTO `opr_order_status` VALUES ('29', '10', 'pending', null, '2017-06-17 10:35:23', 'test');
INSERT INTO `opr_order_status` VALUES ('30', '11', 'pending', '添加珠海訂單', '2017-06-17 10:37:17', 'test');
INSERT INTO `opr_order_status` VALUES ('31', '11', 'sent', '指定技術員，而且發送', '2017-06-17 10:37:42', 'test');
INSERT INTO `opr_order_status` VALUES ('32', '9', 'pending', '物品少算了一個，重新加了一個貨物', '2017-06-17 10:43:29', 'test');
INSERT INTO `opr_order_status` VALUES ('33', '12', 'pending', '', '2017-06-17 10:44:44', 'test');
INSERT INTO `opr_order_status` VALUES ('34', '12', 'pending', '', '2017-06-17 10:45:01', 'test');
INSERT INTO `opr_order_status` VALUES ('35', '12', 'sent', '發送', '2017-06-17 10:45:09', 'test');
INSERT INTO `opr_order_status` VALUES ('36', '12', 'sent', '修訂數量', '2017-06-17 10:46:07', 'test');
INSERT INTO `opr_order_status` VALUES ('37', '12', 'approve', '確定了數量，可以發貨', '2017-06-17 10:52:48', 'test');
