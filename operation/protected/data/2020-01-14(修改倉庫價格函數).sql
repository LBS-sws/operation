

-- ----------------------------
-- Table structure for 修改函數 costPrice（`goods_id` int,`date_time` date）返回值由int改為float
-- ----------------------------
DROP  FUNCTION  costPrice;

CREATE DEFINER = `root`@`localhost` FUNCTION `costPrice`(`goods_id` int,`date_time` date)
 RETURNS float(9,2)
BEGIN
DECLARE cost_price float(9,2);
SELECT price INTO cost_price FROM opr_warehouse_price
WHERE (year<date_format(date_time,'%Y') or (year=date_format(date_time,'%Y') and month<=date_format(date_time,'%m'))) AND warehouse_id = goods_id ORDER BY year DESC,month DESC LIMIT 1;
RETURN cost_price;
END;

