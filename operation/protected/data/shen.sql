/*
Navicat MySQL Data Transfer

Source Server         : vm1
Source Server Version : 50626
Source Host           : 192.168.1.9:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2017-06-29 10:25:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_goods
-- ----------------------------
DROP TABLE IF EXISTS `opr_goods`;
CREATE TABLE `opr_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_code` varchar(10) DEFAULT NULL COMMENT '物品編號',
  `goods_class` varchar(255) DEFAULT NULL COMMENT '物品類型（Import：進口貨、Domestic：國產貨、Fast：快速貨）',
  `name` varchar(30) NOT NULL COMMENT '物品名字',
  `type` varchar(30) NOT NULL COMMENT '物品規格',
  `unit` varchar(30) NOT NULL COMMENT '物品單位',
  `price` varchar(30) NOT NULL COMMENT '物品單價',
  `big_num` int(10) unsigned DEFAULT '0' COMMENT '總部數量限制',
  `small_num` int(10) DEFAULT '0' COMMENT '區域數量限制',
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Records of opr_goods
-- ----------------------------
INSERT INTO `opr_goods` VALUES ('3', '50104', 'Import', '肥皂液-花香味（黑色）', '3桶/箱', '桶', '16.05', '10', '10', null, null, '2017-06-28 14:36:49', '2017-06-28 14:36:49');
INSERT INTO `opr_goods` VALUES ('4', '20104', 'Import', '肥皂液-花香味（紫色）', '2桶/箱', '桶', '14.05', '100', '100', null, null, '2017-06-28 14:36:51', '2017-06-28 14:36:51');
INSERT INTO `opr_goods` VALUES ('5', '00002', 'Import', '肥皂液-花香味（無色）', '7桶/箱', '桶', '8.05', '20', '20', '', '', '2017-06-28 14:36:53', '2017-06-28 14:36:53');
INSERT INTO `opr_goods` VALUES ('6', '10101', 'Domestic', '肥皂液-花香味（紅色）', '4桶/箱', '桶', '11.05', '30', '30', '', '', '2017-06-28 14:36:56', '2017-06-28 14:36:56');
INSERT INTO `opr_goods` VALUES ('7', '10042', 'Domestic', '肥皂液-花香味（黃色）', '5桶/箱', '桶', '21.05', '40', '45', '', '', '2017-06-28 15:37:24', '2017-06-28 15:37:24');
INSERT INTO `opr_goods` VALUES ('8', '12001', 'Fast', '肥皂液-花香味（藍色）', '1桶/箱', '桶', '41.05', '50', '50', '', '', '2017-06-28 14:37:00', '2017-06-28 14:37:00');
INSERT INTO `opr_goods` VALUES ('9', '10001', 'Fast', '肥皂液-花香味（橙色）', '3桶/箱', '桶', '12', '51', '62', '', '', '2017-06-28 14:46:19', '2017-06-28 14:46:19');
INSERT INTO `opr_goods` VALUES ('10', '64211', 'Fast', '白酒', '12瓶/箱', '瓶', '10.2', '1', '0', null, null, '2017-06-29 10:23:44', '2017-06-29 10:23:44');

-- ----------------------------
-- Table structure for opr_order
-- ----------------------------
DROP TABLE IF EXISTS `opr_order`;
CREATE TABLE `opr_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '訂單id',
  `order_code` varchar(10) DEFAULT NULL COMMENT '訂單號（自動生成）',
  `order_user` varchar(30) NOT NULL COMMENT '訂購的用戶',
  `order_class` varchar(30) DEFAULT NULL COMMENT '訂單類型（Import：進口貨、Domestic：國產貨、Fast：快速貨）',
  `activity_id` varchar(10) DEFAULT NULL COMMENT '訂單所屬的活動的id',
  `technician` varchar(10) DEFAULT NULL COMMENT '技術員',
  `status` varchar(30) NOT NULL COMMENT '訂單狀態（pending / sent / approve / reject / cancelled）',
  `city` varchar(30) DEFAULT NULL COMMENT '訂單歸屬城市',
  `remark` varchar(255) DEFAULT NULL COMMENT '備註',
  `judge` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '是否是總部訂單。1：是，0：否',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='訂單表';

-- ----------------------------
-- Records of opr_order
-- ----------------------------

-- ----------------------------
-- Table structure for opr_order_activity
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_activity`;
CREATE TABLE `opr_order_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主鍵',
  `activity_code` varchar(10) DEFAULT NULL COMMENT '活動的編號',
  `activity_title` varchar(50) NOT NULL COMMENT '活動的標題',
  `start_time` date NOT NULL COMMENT '收集開始時間',
  `end_time` date NOT NULL COMMENT '收集結束時間',
  `order_class` varchar(30) DEFAULT NULL COMMENT '訂單類型:Import/Domestic/Fast',
  `num` int(10) unsigned DEFAULT '1' COMMENT '訂單的數量限制',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='訂單權限表';

-- ----------------------------
-- Records of opr_order_activity
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8 COMMENT='訂單內的物品表';

-- ----------------------------
-- Records of opr_order_goods
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8 COMMENT='訂單的狀態表';

-- ----------------------------
-- Records of opr_order_status
-- ----------------------------
