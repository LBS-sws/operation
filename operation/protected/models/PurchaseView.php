<?php

class PurchaseView extends CFormModel
{
	public $id;
	public $activity_code;
	public $activity_title;
	public $start_time;
	public $end_time;
	public $num;
	public $order_class;
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
            'num'=>Yii::t('procurement','Number Restrictions'),
            'order_class'=>Yii::t('procurement','Order Class')
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
                $this->num = $row['num'];
                $this->order_class = $row['order_class'];
                break;
			}
		}
		return true;
	}

	public function getHeaderList() {
		$arr = array(
		    "price_class"=>"Price Class"
        );
		if($this->order_class == "Import"){
		    $arr["weight_class"]="Weight Class";
		    $arr["volume_class"]="Volume Class";
        }
		return $arr;
	}
	public function getBodyList($str) {
	    $html = "";
	    switch ($str){
            case "price_class":
                $goodsClass = $this->getGoodsToActivityId($this->id);
                $html="<p>&nbsp;</p>";
                foreach ($goodsClass as $key => $list){
                    $classifyName = ClassifyForm::getClassifyToId($key);
                    $html.="<legend>$classifyName</legend>";
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
                        //sprintf(".2%",$aaa);
                        $sum+=floatval($goods["price"])*intval($goods["confirm_num"]);
                        $exSum+=intval($goods["goods_num"])*floatval($goods["price"]);
                        $html.="<tr>
                        <td>".$goods["goods_code"]."</td>
                        <td>".$goods["name"]."</td>
                        <td>".sprintf("%.2f",$goods["price"])."</td>
                        <td>".$goods["goods_num"]."</td>
                        <td>".$goods["confirm_num"]."</td>
                        <td>".sprintf("%.2f",intval($goods["goods_num"])*floatval($goods["price"]))."</td>
                        <td>".sprintf("%.2f",floatval($goods["price"])*intval($goods["confirm_num"]))."</td>
                        </tr>";
                    }
                    $html.="</tbody><tfoot><tr><td colspan='5'></td><td class='fa-2x'>".sprintf("%.2f",$exSum)."</td><td class='fa-2x'>".sprintf("%.2f",$sum)."</td></tr></tfoot></table>";
                }
                //$html.="<h3>&nbsp;</h3>";
                break;
            case "weight_class":
                $goodsClass = $this->getGoodsToActivityId($this->id);
                $html="<p>&nbsp;</p>";
                foreach ($goodsClass as $key => $list){
                    $classifyName = ClassifyForm::getClassifyToId($key);
                    $html.="<legend>$classifyName</legend>";
                    $sum = 0;
                    $exSum = 0;
                    $html.="<table class='table table-bordered table-striped'><thead><tr>
                            <th>".Yii::t("procurement","Goods Code")."</th>
                            <th>".Yii::t("procurement","Goods Name")."</th>
                            <th>".Yii::t("procurement","Type")."</th>
                            <th>".Yii::t("procurement","Unit")."</th>
                            <th>".Yii::t("procurement","Gross Weight（kg）")."</th>
                            <th>".Yii::t("procurement","Net Weight（kg）")."</th>
                            <th>".Yii::t("procurement","Confirm Number")."</th>
                            <th>".Yii::t("procurement","Total Gross Weight（kg）")."</th>
                            <th>".Yii::t("procurement","Total Net Weight（kg）")."</th>
                            </tr></thead><tbody>";
                    foreach ($list as $goods){
                        $sum+=floatval($goods["gross_weight"])*intval($goods["confirm_num"]);
                        $exSum+=intval($goods["confirm_num"])*floatval($goods["net_weight"]);
                        $html.="<tr>
                        <td>".$goods["goods_code"]."</td>
                        <td>".$goods["name"]."</td>
                        <td>".$goods["type"]."</td>
                        <td>".$goods["unit"]."</td>
                        <td>".$goods["gross_weight"]."</td>
                        <td>".$goods["net_weight"]."</td>
                        <td>".$goods["confirm_num"]."</td>
                        <td>".(floatval($goods["gross_weight"])*intval($goods["confirm_num"]))."</td>
                        <td>".(floatval($goods["net_weight"])*intval($goods["confirm_num"]))."</td>
                        </tr>";
                    }
                    $html.="</tbody><tfoot><tr><td colspan='7'></td><td class='fa-2x'>$sum</td><td class='fa-2x'>$exSum</td></tr></tfoot></table>";
                }
                break;
            case "volume_class":
                $goodsClass = $this->getGoodsToActivityId($this->id);
                $html="<p>&nbsp;</p>";
                foreach ($goodsClass as $key => $list){
                    $classifyName = ClassifyForm::getClassifyToId($key);
                    $html.="<legend>$classifyName</legend>";
                    $sum = 0;
                    $exSum = 0;
                    $html.="<table class='table table-bordered table-striped'><thead><tr>
                            <th>".Yii::t("procurement","Goods Code")."</th>
                            <th>".Yii::t("procurement","Goods Name")."</th>
                            <th>".Yii::t("procurement","Type")."</th>
                            <th>".Yii::t("procurement","Unit")."</th>
                            <th>".Yii::t("procurement","Length×Width×Height（cm）")."</th>
                            <th>".Yii::t("procurement","Confirm Number")."</th>
                            <th>".Yii::t("procurement","Total Volume")."</th>
                            </tr></thead><tbody>";
                    foreach ($list as $goods){
                        //$exSum+=intval($goods["confirm_num"])*floatval($goods["net_weight"]);
                        $html.="<tr>
                        <td>".$goods["goods_code"]."</td>
                        <td>".$goods["name"]."</td>
                        <td>".$goods["type"]."</td>
                        <td>".$goods["unit"]."</td>
                        <td>".$goods["len"]."×".$goods["width"]."×".$goods["height"]."</td>
                        <td>".$goods["confirm_num"]."</td>
                        <td>"."沒有公式，無法計算"."</td>
                        </tr>";
                    }
                    $html.="</tbody><tfoot><tr><td colspan='6'></td><td class='fa-2x'>&nbsp;</td></tr></tfoot></table>";
                }
                break;
        }
        return $html;
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
                $goods=OrderForm::getOneGoodsToId($row["id"],$row["goods_class"]);//$row["id"]
                if(empty($arr[$goods["classify_id"]][$row["id"]])){
                    $arr[$goods["classify_id"]][$row["id"]] = array(
                        "goods_code"=>$goods["goods_code"],
                        "price"=>$goods["price"],
                        "name"=>$goods["name"],
                        "type"=>$goods["type"],
                        "unit"=>$goods["unit"],
                        "gross_weight"=>empty($goods["gross_weight"])?"":$goods["gross_weight"],
                        "net_weight"=>empty($goods["net_weight"])?"":$goods["net_weight"],
                        "len"=>empty($goods["len"])?"":$goods["len"],
                        "width"=>empty($goods["width"])?"":$goods["width"],
                        "height"=>empty($goods["height"])?"":$goods["height"],
                    );
                }
                if(empty($arr[$goods["classify_id"]][$row["id"]]["goods_num"])){
                    $arr[$goods["classify_id"]][$row["id"]]["goods_num"] = 0;
                }
                if(empty($arr[$goods["classify_id"]][$row["id"]]["confirm_num"])){
                    $arr[$goods["classify_id"]][$row["id"]]["confirm_num"] = 0;
                }
                $arr[$goods["classify_id"]][$row["id"]]["goods_num"]+=intval($row["goods_num"]);
                $arr[$goods["classify_id"]][$row["id"]]["confirm_num"]+=intval($row["confirm_num"]);
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
