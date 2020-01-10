

-- ----------------------------
-- Table structure for 給數據庫添加函數 costPrice（`goods_id` int,`date_time` date）
-- ----------------------------
CREATE DEFINER = `root`@`localhost` FUNCTION `costPrice`(`goods_id` int,`date_time` date)
 RETURNS float
BEGIN
DECLARE cost_price int;
SELECT price INTO cost_price FROM opr_warehouse_price
WHERE (year<date_format(date_time,'%Y') or (year=date_format(date_time,'%Y') and month<=date_format(date_time,'%m'))) AND warehouse_id = goods_id ORDER BY year DESC,month DESC LIMIT 1;
RETURN cost_price;
END;


-- ----------------------------
-- Table structure for opr_order 删除訂單表的總價
-- ----------------------------
ALTER TABLE opr_order DROP COLUMN total_price;

