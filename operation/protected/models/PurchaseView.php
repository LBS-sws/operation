<?php

class PurchaseView extends CFormModel
{
	public $id;
	public $activity_code;
	public $activity_title;
	public $start_time;
	public $end_time;
	public $import_start_time;
	public $import_end_time;
	public $import_num;
	public $domestic_start_time;
    public $domestic_end_time;
    public $domestic_num;
    public $luu;
    public $lcu;
    public $lud;
    public $lcd;

	public function attributeLabels()
	{
		return array(
            'activity_code'=>Yii::t('procurement','Activity Code'),
            'activity_title'=>Yii::t('procurement','Activity Title'),
            'start_time'=>Yii::t('procurement','Start Time'),
            'end_time'=>Yii::t('procurement','End Time'),
            'import_start_time'=>Yii::t('procurement','Import Start Time'),
            'import_end_time'=>Yii::t('procurement','Import End Time'),
            'import_num'=>Yii::t('procurement','Import Num'),
            'domestic_start_time'=>Yii::t('procurement','Domestic Start Time'),
            'domestic_end_time'=>Yii::t('procurement','Domestic End Time'),
            'domestic_num'=>Yii::t('procurement','Domestic Num'),
		);
	}


	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order_activity")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->activity_code = $row['activity_code'];
                $this->activity_title = $row['activity_title'];
                $this->start_time = $row['start_time'];
                $this->end_time = $row['end_time'];
                $this->import_start_time = $row['import_start_time'];
                $this->import_end_time = $row['import_end_time'];
                $this->import_num = $row['import_num'];
                $this->domestic_start_time = $row['domestic_start_time'];
                $this->domestic_end_time = $row['domestic_end_time'];
                $this->domestic_num = $row['domestic_num'];
                break;
			}
		}
		return true;
	}

	public function getHeaderList() {
		return array(
		    "order_class"=>"Order Class",
		    "goods_class"=>"Goods Class",
		    "city_class"=>"City Class"
        );
	}
	public function getBodyList($str) {
	    $html = "";
	    switch ($str){
            case "order_class":
                $orderClass = PurchaseList::getOrderSumToId($this->id);
                $sum = 0;
                $html="<p>&nbsp;</p><div class='col-lg-offset-3 col-lg-6'><table class='table table-bordered table-striped'><thead><tr>
                    <th>".Yii::t("procurement","Order Class")."</th>
                    <th>".Yii::t("procurement","Order Sum")."</th>
                    </tr></thead><tbody>";
                foreach ($orderClass["list"] as $key => $num){
                    $sum+=intval($num);
                    $html.="<tr>
                        <td>".Yii::t("procurement",$key)."</td>
                        <td>".$num."</td>
                        </tr>";
                }
                $html.="</tbody><tfoot><td>&nbsp;</td><td class='fa-2x'>$sum</td></tfoot></table></div>";
                break;
            case "goods_class":
                $goodsClass = $this->getGoodsToActivityId($this->id);
                $html="<p>&nbsp;</p>";
                foreach ($goodsClass as $key => $list){
                    $html.="<legend>".Yii::t("procurement",$key)."</legend>";
                    $sum = 0;
                    $exSum = 0;
                    $html.="<table class='table table-bordered table-striped'><thead><tr>
                            <th>".Yii::t("procurement","Goods Code")."</th>
                            <th>".Yii::t("procurement","Goods Name")."</th>
                            <th>".Yii::t("procurement","Price（RMB）")."</th>
                            <th>".Yii::t("procurement","Goods Number")."</th>
                            <th>".Yii::t("procurement","Confirm Number")."</th>
                            <th>".Yii::t("procurement","expected to price（RMB）")."</th>
                            <th>".Yii::t("procurement","Total（RMB）")."</th>
                            </tr></thead><tbody>";
                    foreach ($list as $goods){
                        $sum+=floatval($goods["price"])*intval($goods["confirm_num"]);
                        $exSum+=intval($goods["goods_num"])*floatval($goods["price"]);
                        $html.="<tr>
                        <td>".$goods["goods_code"]."</td>
                        <td>".$goods["name"]."</td>
                        <td>".$goods["price"]."</td>
                        <td>".$goods["goods_num"]."</td>
                        <td>".$goods["confirm_num"]."</td>
                        <td>".(intval($goods["goods_num"])*floatval($goods["price"]))."</td>
                        <td>".(floatval($goods["price"])*intval($goods["confirm_num"]))."</td>
                        </tr>";
                    }
                    $html.="</tbody><tfoot><tr><td colspan='5'></td><td class='fa-2x'>$exSum</td><td class='fa-2x'>$sum</td></tr></tfoot></table>";
                }
                break;
            case "city_class":
                $cityClass = $this->getCityClassToActivityId($this->id);
                $sum = 0;
                $html="<p>&nbsp;</p>";
                $html.="<table class='table table-bordered table-striped'><thead><tr>
                            <th>".Yii::t("procurement","City Class")."</th>
                            <th>".Yii::t("procurement","Import").Yii::t("procurement","Order")."</th>
                            <th>".Yii::t("procurement","Domestic").Yii::t("procurement","Order")."</th>
                            <th>".Yii::t("procurement","Fast").Yii::t("procurement","Order")."</th>
                            <th>".Yii::t("procurement","Order Sum")."</th>
                            </tr></thead><tbody>";
                foreach ($cityClass as $key => $list){
                    $sum +=$list["Import"]+$list["Domestic"]+$list["Fast"];
                    $html.="<tr>
                        <td>".Yii::t("procurement",$key)."</td>
                        <td>".$list["Import"]."</td>
                        <td>".$list["Domestic"]."</td>
                        <td>".$list["Fast"]."</td>
                        <td>".($list["Import"]+$list["Domestic"]+$list["Fast"])."</td>
                        </tr>";
                }
                $html.="</tbody><tfoot><tr><td colspan='4'></td><td class='fa-2x'>$sum</td></tr></tfoot></table>";
                break;
        }
        return $html;
	}

	//根據物品id獲取物品信息
    public function getGoodsToGoodsId($goods_id){
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_goods")
            ->where('id = :id',array(':id'=>$goods_id))
            ->queryAll();
        if($rows){
            return $rows[0];
        }else{
            return array();
        }
    }

	public function getGoodsToActivityId($activity_id){
        $arr=array();
        $rows = Yii::app()->db->createCommand()->select("c.goods_id as id,b.order_class as goods_class,c.goods_num,c.confirm_num")
            ->from("opr_order b,opr_order_goods c")
            ->where('b.id = c.order_id AND b.activity_id = :activity_id AND b.judge=1 AND b.status != "pending" AND b.status != "cancelled"',
                array(':activity_id'=>$activity_id))
            //->leftJoin("opr_goods d","c.goods_id = d.id")
            ->queryAll();
        // AND c.goods_id = d.id
        if($rows){
            foreach ($rows as $row){
                if(empty($arr[$row["goods_class"]][$row["id"]])){
                    $goods=$this->getGoodsToGoodsId($row["id"]);
                    $arr[$row["goods_class"]][$row["id"]] = array(
                        "goods_code"=>$goods["goods_code"],
                        "price"=>$goods["price"],
                        "goods_class"=>$goods["goods_class"],
                        "name"=>$goods["name"],
                        "type"=>$goods["type"],
                        "unit"=>$goods["unit"],
                    );
                }
                if(empty($arr[$row["goods_class"]][$row["id"]]["goods_num"])){
                    $arr[$row["goods_class"]][$row["id"]]["goods_num"] = 0;
                }
                if(empty($arr[$row["goods_class"]][$row["id"]]["confirm_num"])){
                    $arr[$row["goods_class"]][$row["id"]]["confirm_num"] = 0;
                }
                $arr[$row["goods_class"]][$row["id"]]["goods_num"]+=intval($row["goods_num"]);
                $arr[$row["goods_class"]][$row["id"]]["confirm_num"]+=intval($row["confirm_num"]);
            }
        }
        return $arr;
    }

	public function getCityClassToActivityId($activity_id){
        $arr=array();
        $rows = Yii::app()->db->createCommand()->select("order_class,city")
            ->from("opr_order")
            ->where("activity_id = :activity_id AND judge=1 AND status != 'pending' AND status != 'cancelled'", array(':activity_id'=>$activity_id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(empty($arr[$row["city"]])){
                    $arr[$row["city"]] = array(
                        "Import"=>0,
                        "Domestic"=>0,
                        "Fast"=>0
                    );
                }
                $arr[$row["city"]][$row["order_class"]]++;
            }
        }
        return $arr;
    }
}
