/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operationdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2024-06-12 15:29:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_api_curl
-- ----------------------------
DROP TABLE IF EXISTS `opr_api_curl`;
CREATE TABLE `opr_api_curl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status_type` char(255) NOT NULL DEFAULT 'p' COMMENT '状态，p:未发送 C：已完成 E：响应异常',
  `min_url` varchar(255) NOT NULL,
  `info_type` varchar(255) NOT NULL,
  `info_url` varchar(255) NOT NULL COMMENT '接口地址',
  `data_content` longtext NOT NULL COMMENT '发送的curl（json字符串）',
  `out_content` text COMMENT '响应的内容',
  `message` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='已发送的CURL';

-- ----------------------------
-- Table structure for opr_store
-- ----------------------------
DROP TABLE IF EXISTS `opr_store`;
CREATE TABLE `opr_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '仓库名字',
  `jd_store_no` varchar(100) NOT NULL COMMENT '金蝶仓库编号',
  `store_type` int(1) NOT NULL DEFAULT '1' COMMENT '仓库类型 1：默认 2：正常',
  `z_display` int(1) NOT NULL DEFAULT '1' COMMENT '是否显示 1：显示 0：隐藏',
  `city` varchar(50) NOT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='仓库名称（金蝶）';

-- ----------------------------
-- Table structure for opr_token
-- ----------------------------
DROP TABLE IF EXISTS `opr_token`;
CREATE TABLE `opr_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) NOT NULL COMMENT 'token',
  `expires_in` varchar(255) DEFAULT NULL COMMENT 'token有效时长',
  `start_date` datetime DEFAULT NULL COMMENT '开始时间',
  `end_date` datetime DEFAULT NULL COMMENT '到期时间',
  `language` varchar(255) DEFAULT NULL,
  `token_type` varchar(30) NOT NULL DEFAULT 'JD' COMMENT 'token类型：JD：金蝶系统',
  `token_json` text,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='跨域访问的token';

-- ----------------------------
-- Table structure for opr_order_goods_store
-- ----------------------------
DROP TABLE IF EXISTS `opr_order_goods_store`;
CREATE TABLE `opr_order_goods_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_goods_id` int(100) NOT NULL COMMENT '领料物品id',
  `store_id` int(100) NOT NULL COMMENT '仓库id',
  `store_num` varchar(10) NOT NULL DEFAULT '0' COMMENT '发货数量',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='技术员领料的发货需要指定仓库';

-- ----------------------------
-- Table structure for opr_send_set_jd
-- ----------------------------
DROP TABLE IF EXISTS `opr_send_set_jd`;
CREATE TABLE `opr_send_set_jd` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_id` varchar(255) NOT NULL,
  `set_type` varchar(255) NOT NULL DEFAULT 'warehouse',
  `field_id` varchar(255) NOT NULL,
  `field_value` varchar(255) DEFAULT NULL,
  `field_type` varchar(255) DEFAULT 'text',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` datetime DEFAULT NULL,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='金蝶关联的配置表';

-- ----------------------------
-- Table structure for opr_warehouse
-- ----------------------------
alter table opr_warehouse_price add city varchar(100) NULL DEFAULT NULL COMMENT '价格所在城市' after price;
alter table opr_warehouse add local_bool int(11) NOT NULL DEFAULT 1 COMMENT '是否本地物料 0:否 1：是' after display;
alter table opr_warehouse add old_good_no varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '旧物品编号' after display;
alter table opr_warehouse add `jd_classify_no`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '金蝶类别编号' after display;
alter table opr_warehouse add `jd_classify_name`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '金蝶类别名称' after display;
update opr_warehouse set old_good_no=goods_code where old_good_no is null;
update opr_warehouse set jd_classify_no=classify_id where jd_classify_no is null;
update opr_warehouse set jd_classify_name=(select name from opr_classify a where a.id=classify_id) where jd_classify_name is null;
update opr_warehouse_price set city=(select city from opr_warehouse a where a.id=warehouse_id) where city is null;

-- ----------------------------
-- Table structure for opr_warehouse_back
-- ----------------------------
alter table opr_warehouse_back add store_id int(11) NULL DEFAULT NULL COMMENT '仓库id' after warehouse_id;

