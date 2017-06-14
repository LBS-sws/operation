/*
Navicat MySQL Data Transfer

Source Server         : vm1
Source Server Version : 50626
Source Host           : 192.168.1.7:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2017-06-14 17:08:02
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Records of opr_goods
-- ----------------------------
INSERT INTO `opr_goods` VALUES ('3', '肥皂液-花香味（黑色）', '3桶/箱', '桶', '16.05', null, null, '2017-06-13 16:11:03', '2017-06-13 16:11:03');
INSERT INTO `opr_goods` VALUES ('4', '肥皂液-花香味（紫色）', '2桶/箱', '桶', '14.05', null, null, '2017-06-13 16:06:17', '2017-06-13 16:06:17');
INSERT INTO `opr_goods` VALUES ('5', '肥皂液-花香味（無色）', '7桶/箱', '桶', '16.05', null, null, '2017-06-13 16:12:35', '2017-06-13 16:12:35');

-- ----------------------------
-- Table structure for opr_order
-- ----------------------------
DROP TABLE IF EXISTS `opr_order`;
CREATE TABLE `opr_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '訂單id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '物品id',
  `order_num` int(10) unsigned NOT NULL COMMENT '訂購數量',
  `confirm_num` varchar(10) DEFAULT NULL COMMENT '實際數量',
  `order_user` varchar(30) NOT NULL COMMENT '訂購的用戶',
  `technician` varchar(10) DEFAULT NULL COMMENT '技術員',
  `status` varchar(30) NOT NULL COMMENT '訂單狀態（pending / sent / approve / reject / cancelled）',
  `remark` varchar(255) DEFAULT NULL COMMENT '備註',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='訂單表';

-- ----------------------------
-- Records of opr_order
-- ----------------------------
INSERT INTO `opr_order` VALUES ('4', '3', '113', null, 'test', 'test', 'cancelled', null, 'test', 'test', '2017-06-14 16:23:47', '2017-06-14 16:23:47');
INSERT INTO `opr_order` VALUES ('5', '5', '22', '4', 'test', 'test', 'sent', '', 'test', 'test', '2017-06-14 15:08:05', '2017-06-14 15:08:05');
INSERT INTO `opr_order` VALUES ('6', '5', '2', '', 'shenchao', 'test', 'approve', '可以採購', 'shenchao', 'shenchao', '2017-06-14 15:58:10', '2017-06-14 15:58:10');
INSERT INTO `opr_order` VALUES ('7', '5', '7', null, 'shenchao', null, 'pending', null, 'shenchao', 'shenchao', null, null);
INSERT INTO `opr_order` VALUES ('8', '5', '4', '5', 'test', 'shenchao', 'reject', '數量不足', 'test', 'test', '2017-06-14 16:48:08', '2017-06-14 16:48:08');

-- ----------------------------
-- Table structure for opr_order_status
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_status`;
CREATE TABLE `opr_order_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '訂單狀態id',
  `order_id` int(10) unsigned NOT NULL,
  `status` varchar(30) NOT NULL COMMENT '訂單狀態（pending / sent / approve / reject / cancelled）',
  `time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '訂單狀態時間',
  `lcu` varchar(255) NOT NULL COMMENT '操作人員',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='訂單的狀態表';

-- ----------------------------
-- Records of opr_order_status
-- ----------------------------
INSERT INTO `opr_order_status` VALUES ('6', '4', 'pending', '2017-06-14 13:05:41', 'test');
INSERT INTO `opr_order_status` VALUES ('7', '4', 'cancelled', '2017-06-14 13:21:48', 'test');
INSERT INTO `opr_order_status` VALUES ('8', '5', 'pending', '2017-06-14 13:22:37', 'test');
INSERT INTO `opr_order_status` VALUES ('9', '5', 'sent', '2017-06-14 13:22:48', 'test');
INSERT INTO `opr_order_status` VALUES ('10', '5', 'sent', '2017-06-14 15:00:17', 'test');
INSERT INTO `opr_order_status` VALUES ('11', '5', 'sent', '2017-06-14 15:00:26', 'test');
INSERT INTO `opr_order_status` VALUES ('12', '5', 'sent', '2017-06-14 15:08:07', 'test');
INSERT INTO `opr_order_status` VALUES ('13', '4', 'cancelled', '2017-06-14 15:21:42', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('14', '6', 'pending', '2017-06-14 15:29:24', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('15', '6', 'sent', '2017-06-14 15:52:16', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('16', '6', 'approve', '2017-06-14 15:58:12', 'test');
INSERT INTO `opr_order_status` VALUES ('17', '7', 'pending', '2017-06-14 16:18:51', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('18', '4', 'cancelled', '2017-06-14 16:22:54', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('19', '4', 'cancelled', '2017-06-14 16:23:49', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('20', '8', 'pending', '2017-06-14 16:25:45', 'test');
INSERT INTO `opr_order_status` VALUES ('21', '8', 'pending', '2017-06-14 16:25:52', 'test');
INSERT INTO `opr_order_status` VALUES ('22', '8', 'pending', '2017-06-14 16:26:21', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('23', '8', 'pending', '2017-06-14 16:28:07', 'test');
INSERT INTO `opr_order_status` VALUES ('24', '8', 'pending', '2017-06-14 16:28:23', 'test');
INSERT INTO `opr_order_status` VALUES ('25', '8', 'sent', '2017-06-14 16:28:32', 'test');
INSERT INTO `opr_order_status` VALUES ('26', '8', 'reject', '2017-06-14 16:29:35', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('27', '8', 'reject', '2017-06-14 16:45:22', 'shenchao');
INSERT INTO `opr_order_status` VALUES ('28', '8', 'reject', '2017-06-14 16:48:11', 'shenchao');
