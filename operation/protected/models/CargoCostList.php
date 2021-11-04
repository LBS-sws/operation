<?php

class CargoCostList extends CListPageModel
{
    public $year;//年份
    public $month;//月份
    public $city;//查詢的城市
    public $total_price;//總額
    public function attributeLabels()
    {
        return array(
            'city'=>Yii::t('procurement','Order For City'),
            'lcu'=>Yii::t('procurement','Apply for user'),
            'total_price'=>Yii::t('procurement','Cargo Cost'),
        );
    }

    public function rules(){
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, city, year, month','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        if(empty($this->year)||!is_numeric($this->year)){
            $this->year = date("Y", strtotime("-1 month"));
            $this->month = intval(date("m", strtotime("-1 month")));
        }
        //$this->month = (empty($this->month)||!is_numeric($this->month))?date("m"):$this->month;
        //,ifnull(SUM(costPrice(b.goods_id,a.lcd)*CONVERT(b.confirm_num,DECIMAL)),0) as total_min
        $suffix =  Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $userName = Yii::app()->user->name;
        //查詢列表
        $sql1 = "select b.username,b.disp_name,c.name AS s_city,SUM(a.total_price) as total_price
				from opr_order a 
				LEFT JOIN security$suffix.sec_user b ON a.lcu=b.username 
				LEFT JOIN security$suffix.sec_city c ON a.city=c.code 
				where (a.city in ($city_allow) AND a.judge=0 AND a.status IN ('approve','finished')) 
			";
        //總條數用的sql
        $sql2 = "select b.username  
				from opr_order a 
				LEFT JOIN security$suffix.sec_user b ON a.lcu=b.username 
				LEFT JOIN security$suffix.sec_city c ON a.city=c.code 
				where (a.city in ($city_allow) AND a.judge=0 AND a.status IN ('approve','finished')) 
			";
        //總價用的sql
        $sql3 = "select SUM(a.total_price) as total_price
				from opr_order a
				LEFT JOIN security$suffix.sec_user b ON a.lcu=b.username 
				LEFT JOIN security$suffix.sec_city c ON a.city=c.code 
				where (a.city in ($city_allow) AND a.judge=0 AND a.status IN ('approve','finished')) 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'lcu':
                    $clause .= General::getSqlConditionClause('b.disp_name', $svalue);
                    break;
                case 'city':
                    $clause .= General::getSqlConditionClause('c.name', $svalue);
                    break;
            }
        }
        if (!empty($this->month)) {
            $svalue = str_replace("'","\'",$this->month);
            $svalue = is_numeric($svalue)?intval($svalue):1;
            $svalue = $svalue<10?"0$svalue":$svalue;
            $svalue = $this->year."/".$svalue;
            $clause .= " and date_format(a.lcd,'%Y/%m') ='$svalue' ";
        }else{
            $svalue = $this->year;
            $clause .= " and date_format(a.lcd,'%Y') ='$svalue' ";
        }
        if (!empty($this->city)) {
            $svalue = str_replace("'","\'",$this->city);
            $clause .= " and b.city ='$svalue' ";
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by total_price desc";

        $sql = "select count(*) from ($sql2 $clause GROUP BY b.username) ttt";
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
        $this->total_price = Yii::app()->db->createCommand($sql3.$clause)->queryScalar();

        $sql = $sql1.$clause." GROUP BY b.username,b.disp_name,c.name".$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'username'=>$record['username'],
                    'total_price'=>sprintf("%.2f",$record['total_price']),
                    'city'=>$record['s_city'],
                    'lcu'=>$record['disp_name'],
                );
            }
        }
        $session = Yii::app()->session;
        $session['cargoCost_ya01'] = $this->getCriteria();
        return true;
    }

    public function getCriteria() {
        return array(
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'city'=>$this->city,
            'year'=>$this->year,
            'month'=>$this->month,
        );
    }

//獲取城市列表
    public function getCityAllList()
    {
        $city_allow = Yii::app()->user->city_allow();
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand()->select("code,name")->from($from)->where("code in ($city_allow)")->queryAll();
        $arr = array(""=>" -- ".Yii::t("user","City")." -- ");
        foreach ($rows as $row){
            $arr[$row["code"]] = $row["name"];
        }
        return $arr;
    }

//獲取年份列表
    public function getYearList()
    {
        $arr=array();
        $num = intval(date("Y"));
        for ($i = $num;$i>$num-5;$i--){
            $arr[$i] = $i.Yii::t("monthly","Year");
        }
        return $arr;
    }

//獲取月份列表
    public function getMonthList()
    {
        $arr = array(""=>" -- 月份 -- ");
        for ($i = 1;$i<13;$i++){
            $value = $i<10?"0$i":$i;
            $arr[$i] = $value.Yii::t("monthly","Month");
        }
        return $arr;
    }

    //倉庫價格導入後，需要修改物品單價
    public static function resetGoodsPrice($year=0,$month=0,$city=''){
        $whereSql="";
        if(!empty($year)){
            $whereSql.=" and a.year='{$year}'";
        }
        if(!empty($month)){
            $whereSql.=" and a.month='{$month}'";
        }
        if(!empty($city)){
            $whereSql.=" and b.city='{$city}'";
        }
        $inSql = Yii::app()->db->createCommand()->select("warehouse_id")
            ->from("opr_warehouse_price")->where("new_num=1 $whereSql")
            ->group("warehouse_id")->getText();
        $warehouseRows = Yii::app()->db->createCommand()->select("a.id,a.warehouse_id,a.year,a.month,a.price,a.new_num,a.lcd")
            ->from("opr_warehouse_price a")
            ->leftJoin("opr_warehouse b","a.warehouse_id=b.id")
            ->where("a.warehouse_id in ($inSql)$whereSql")->order("a.warehouse_id asc,a.year desc,a.month desc")->queryAll();
        if($warehouseRows){
            $minLcd = "2021/11/01";
            $prevRow=array();
            foreach ($warehouseRows as $priceRow){
                $lcd = General::toDate($priceRow["lcd"]);
                $minLcd = $lcd<$minLcd?$lcd:$minLcd;
                $priceRow["month"] = floatval($priceRow["month"]);
                $priceRow["month"] = $priceRow["month"]<10?"0".$priceRow["month"]:$priceRow["month"];
                $maxYear = $priceRow["year"]."/".$priceRow["month"];
                $sqlExpr = " and date_format(b.lcd,'%Y/%m')>='$maxYear'";
                if(!empty($prevRow)&&$prevRow["warehouse_id"]==$priceRow["warehouse_id"]){
                    $minYear = $prevRow["year"]."/".$prevRow["month"];
                    $sqlExpr.= " and date_format(b.lcd,'%Y/%m')<'$minYear'";
                }
                $prevRow = $priceRow;
                if($priceRow["new_num"]==1){
                    //修改訂單表的物品價格
                    $price = empty($priceRow["price"])?0:floatval($priceRow["price"]);
                    $orderGoods=Yii::app()->db->createCommand()->select("a.id,a.confirm_num,a.order_id")
                        ->from("opr_order_goods a")
                        ->leftJoin("opr_order b","a.order_id=b.id")
                        ->where("a.goods_id='{$priceRow['warehouse_id']}' $sqlExpr")->queryAll();
                    if($orderGoods){//查詢所有包含該物品的發貨數量
                        foreach ($orderGoods as $good){
                            if(!empty($good["confirm_num"])){
                                $num = !is_numeric($good["confirm_num"])?0:floatval($good["confirm_num"]);
                                $goodPrice = round($price*$num,4);
                                //echo "orderId:{$good['order_id']},orderInfoId:{$good['id']},confirmNum:{$num},goodPrice:{$goodPrice}\n";
                                Yii::app()->db->createCommand()->update('opr_order_goods', array(
                                    'total_price'=>$goodPrice>999999?0:$goodPrice,
                                ), "id=:id", array(':id'=>$good["id"]));
                            }
                        }
                    }
                    //價格修改完成
                    Yii::app()->db->createCommand()->update('opr_warehouse_price', array(
                        'new_num'=>0,
                    ), "id=:id", array(':id'=>$priceRow["id"]));
                }
            }
            //更新訂單總價
            Yii::app()->db->createCommand("update opr_order a set a.total_price=(SELECT sum(b.total_price) FROM opr_order_goods b WHERE b.order_id=a.id AND date_format(b.lcd,'%Y/%m/%d')>='$minLcd') WHERE date_format(a.lcd,'%Y/%m/%d')>='$minLcd'")->execute();
        }
    }
}
