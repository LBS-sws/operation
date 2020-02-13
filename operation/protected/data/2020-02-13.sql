/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-02-13 09:50:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_city_price
-- ----------------------------
DROP TABLE IF EXISTS `opr_city_price`;
CREATE TABLE `opr_city_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(10) NOT NULL,
  `price_type` int(2) NOT NULL DEFAULT '1' COMMENT '1:选择第一个单价  2：选择第二个单价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='城市分配价格表（进口货物品选择价格1或者价格2）';

-- ----------------------------
-- Table structure for opr_goods_im
-- ----------------------------
alter table opr_goods_im add price_two varchar(30) NULL DEFAULT NULL COMMENT '进口货物品单价2' after price;
alter table opr_goods_im add customs_code varchar(255) NULL DEFAULT NULL COMMENT '海关编号' after origin;
alter table opr_goods_im add customs_name varchar(255) NULL DEFAULT NULL COMMENT '海关名字' after customs_code;
alter table opr_goods_im add inspection varchar(255) NULL DEFAULT NULL COMMENT '商检' after customs_name;

-- ----------------------------
-- Table structure for opr_order_goods
-- ----------------------------
alter table opr_order_goods add batch_code varchar(30) NULL DEFAULT NULL COMMENT '批次號碼(进口货专用）' after city;
alter table opr_order_goods add etd varchar(100) NULL DEFAULT NULL COMMENT 'etd(进口货专用）' after batch_code;
