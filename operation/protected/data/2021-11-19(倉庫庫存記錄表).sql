/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-11-19 14:09:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for opr_warehouse_history
-- ----------------------------
DROP TABLE IF EXISTS `opr_warehouse_history`;
CREATE TABLE `opr_warehouse_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apply_date` datetime NOT NULL COMMENT '记录日期',
  `warehouse_id` int(11) NOT NULL COMMENT '库存id',
  `old_sum` varchar(11) NOT NULL COMMENT '庫存數量(变更前)',
  `now_sum` varchar(11) NOT NULL COMMENT '庫存數量(变更后)',
  `apply_name` varchar(255) NOT NULL COMMENT '操作人员的登录账户（昵称）',
  `status_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:庫存修改 2：订货新增 3：订货修改 4:订货退回',
  `order_code` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='倉庫庫存历史表';
