/*
Navicat MySQL Data Transfer

Source Server         : vm1
Source Server Version : 50626
Source Host           : 192.168.1.10:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2017-06-27 15:40:05
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
  `lcu` varchar(30) CHARACTER SET utf32 DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='物品表';

-- ----------------------------
-- Records of opr_goods
-- ----------------------------
INSERT INTO `opr_goods` VALUES ('3', '50104', 'Import', '肥皂液-花香味（黑色）', '3桶/箱', '桶', '16.05', null, null, '2017-06-21 16:47:55', '2017-06-21 16:47:55');
INSERT INTO `opr_goods` VALUES ('4', '20104', 'Import', '肥皂液-花香味（紫色）', '2桶/箱', '桶', '14.05', null, null, '2017-06-21 16:47:44', '2017-06-21 16:47:44');
INSERT INTO `opr_goods` VALUES ('5', '00002', 'Import', '肥皂液-花香味（無色）', '7桶/箱', '桶', '8.05', '', '', '2017-06-21 16:47:32', '2017-06-21 16:47:32');
INSERT INTO `opr_goods` VALUES ('6', '10101', 'Domestic', '肥皂液-花香味（紅色）', '4桶/箱', '桶', '11.05', '', '', '2017-06-21 16:46:44', '2017-06-21 16:46:44');
INSERT INTO `opr_goods` VALUES ('7', '10042', 'Domestic', '肥皂液-花香味（黃色）', '5桶/箱', '桶', '21.05', '', '', '2017-06-21 16:46:32', '2017-06-21 16:46:32');
INSERT INTO `opr_goods` VALUES ('8', '12001', 'Fast', '肥皂液-花香味（藍色）', '1桶/箱', '桶', '41.05', '', '', '2017-06-21 16:46:17', '2017-06-21 16:46:17');
INSERT INTO `opr_goods` VALUES ('9', '10001', 'Fast', '肥皂液-花香味（橙色）', '3桶/箱', '桶', '12', '', '', '2017-06-21 16:45:55', '2017-06-21 16:45:55');

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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='訂單表';

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
  `import_start_time` date DEFAULT NULL COMMENT '國外訂單開始時間',
  `import_end_time` date DEFAULT NULL COMMENT '國外訂單結束時間',
  `import_num` int(10) unsigned DEFAULT NULL COMMENT '每個區域國外訂單數量',
  `domestic_start_time` date DEFAULT NULL COMMENT '國內貨開始時間',
  `domestic_end_time` date DEFAULT NULL COMMENT '國內貨結束時間',
  `domestic_num` int(10) unsigned DEFAULT NULL COMMENT '每個區域國內貨訂單數量',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='訂單權限表';

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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COMMENT='訂單內的物品表';

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
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8 COMMENT='訂單的狀態表';

-- ----------------------------
-- Records of opr_order_status
-- ----------------------------
INSERT INTO `opr_order_status` VALUES ('91', '29', 'pending', '', '2017-06-27 13:37:44', 'test');
INSERT INTO `opr_order_status` VALUES ('92', '29', 'pending', '测试', '2017-06-27 13:38:27', 'test');
INSERT INTO `opr_order_status` VALUES ('93', '29', 'sent', '求发货', '2017-06-27 13:53:28', 'test');
INSERT INTO `opr_order_status` VALUES ('94', '29', 'approve', '仓库库存只有2个，所以发2个', '2017-06-27 15:00:13', 'test');
INSERT INTO `opr_order_status` VALUES ('95', '29', 'finished', '物品确定少拿了一个，希望下次多拿一点', '2017-06-27 15:01:37', 'test');
INSERT INTO `opr_order_status` VALUES ('98', '31', 'pending', '', '2017-06-27 15:03:41', 'test');
INSERT INTO `opr_order_status` VALUES ('99', '31', 'sent', '求发货', '2017-06-27 15:03:51', 'test');
INSERT INTO `opr_order_status` VALUES ('101', '31', 'reject', '求发货', '2017-06-27 15:07:33', 'test');
