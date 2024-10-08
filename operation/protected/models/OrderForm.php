<?php
//2024年9月28日09:28:46

class OrderForm extends CFormModel
{
	public $id=0;
	public $order_user;
	//public $technician;
    public $status;
    public $city;
    public $remark;
	public $luu;
	public $lcu;
	public $statusList;
	public $order_code;
	public $order_class;
	public $activity_id;
	public $goods_list;
	public $ject_remark;
	public $fish_remark;
	public $acc_bool=false;

	public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->status = "pending";
        $this->goods_list = array(
            array(
            "id"=>"",
            "goods_id"=>"",
            "classify_id"=>"",
            "stickies_id"=>"",
            "note"=>"",
            "name"=>"",
            "type"=>"",
            "unit"=>"",
            "price"=>"",
            "goods_num"=>"",
            )
        );
    }

    public function attributeLabels()
	{
		return array(
            'order_code'=>Yii::t('procurement','Order Code'),
            'order_class'=>Yii::t('procurement','Order Class'),
            'activity_id'=>Yii::t('procurement','Order of Activity'),
            'goods_list'=>Yii::t('procurement','Goods List'),
            'order_user'=>Yii::t('procurement','Order User'),
            //'technician'=>Yii::t('procurement','Technician'),
            'status'=>Yii::t('procurement','Order Status'),
            'remark'=>Yii::t('procurement','Remark'),
            'ject_remark'=>Yii::t('procurement','reject remark'),
            'fish_remark'=>Yii::t('procurement','finish remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city, order_code, goods_list, order_user, order_class, activity_id, technician, status, remark, ject_remark, fish_remark, luu, lcu, lud, lcd','safe'),
            array('goods_list','required','on'=>array("new","edit","audit")),
            array('goods_list','validateGoods','on'=>array("new","edit","audit")),
            //array('activity_id','required','on'=>'audit'),
            array('remark','required','on'=>'reject'),
            array('fish_remark','required','on'=>'finish'),
            array('activity_id','validateActivity','on'=>'audit'),
            //array('order_num','numerical','allowEmpty'=>true,'integerOnly'=>true),
            //array('order_num','in','range'=>range(0,600)),
		);
	}

	//驗證訂單內的物品
    public function validateGoods($attribute, $params){
	    $goods_list = $this->goods_list;
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
            return false;
        }
        $validateList = array();
        foreach ($goods_list as $key =>$goods){
            if(empty($goods["goods_id"]) && empty($goods["goods_num"])){
                unset($this->goods_list[$key]);
            }else if (empty($goods["goods_id"]) || empty($goods["goods_num"])){
                $message = Yii::t('procurement','The goods or quantity cannot be empty');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["goods_id"])|| floor($goods["goods_id"])!=$goods["goods_id"]){
                $message = Yii::t('procurement','goods does not exist');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["goods_num"])|| floor($goods["goods_num"])!=$goods["goods_num"]){
                $message = Yii::t('procurement','Goods Number can only be numbered');
                $this->addError($attribute,$message);
                return false;
            }else{
                $list = $this->getOneGoodsToId($goods["goods_id"],$this->order_class);
                if (empty($list)){
                    $message = Yii::t('procurement','Not Font Goods').$goods["name"];
                    $this->addError($attribute,$message);
                    return false;
                }else{
                    if(empty($list["rules_id"])){
                        //常規驗證
                        if (intval($goods["goods_num"])%intval($list["multiple"]) != 0){
                            $message = $list["name"]." ".Yii::t('procurement','Multiple is').$list["multiple"];
                            $this->addError($attribute,$message);
                            return false;
                        }elseif (intval($list["big_num"])<intval($goods["goods_num"])){
                            $message = $list["name"]." ".Yii::t('procurement','Max Number is').$list["big_num"];
                            $this->addError($attribute,$message);
                            return false;
                        }elseif (intval($list["small_num"])>intval($goods["goods_num"])){
                            $message = $list["name"]." ".Yii::t('procurement','Min Number is').$list["small_num"];
                            $this->addError($attribute,$message);
                            return false;
                        }
                    }else{
                        //混合驗證
                        if(empty($validateList[$list["rules_id"]])){
                            $rules = RulesForm::getRulesToId($list["rules_id"]);
                            $validateList[$list["rules_id"]] = array(
                                "rulesName"=>$rules["name"],
                                "rulesMultiple"=>$rules["multiple"],
                                "rulesMin"=>$rules["min"],
                                "rulesMax"=>$rules["max"],
                                "goodsNum"=>0,
                            );
                        }
                        $validateList[$list["rules_id"]]["goodsNum"]+=intval($goods["goods_num"]);
                    }
                }
            }
        }
        foreach ($validateList as $hybrid){
            if (intval($hybrid["goodsNum"])%intval($hybrid["rulesMultiple"]) != 0){
                $message = $hybrid["rulesName"]." ".Yii::t('procurement','Multiple is').$hybrid["rulesMultiple"];
                $this->addError($attribute,$message);
                return false;
            }elseif (intval($hybrid["rulesMax"])<intval($hybrid["goodsNum"])){
                $message = $hybrid["rulesName"]." ".Yii::t('procurement','Max Number is').$hybrid["rulesMax"];
                $this->addError($attribute,$message);
                return false;
            }elseif (intval($hybrid["rulesMin"])>intval($hybrid["goodsNum"])){
                $message = $hybrid["rulesName"]." ".Yii::t('procurement','Min Number is').$hybrid["rulesMin"];
                $this->addError($attribute,$message);
                return false;
            }
        }
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
        }
    }
    public function validateActivity($attribute, $params){
        $city = Yii::app()->user->city();
        if($this->scenario == "audit" && $this->order_class != "Fast"){
            $arrBool = OrderAccForm::getNowOrderAcc();
            //不允許多個訂單同時進行
            if(empty($arrBool[$this->order_class])){
                $rows = Yii::app()->db->createCommand()->select("count(id)")
                    ->from("opr_order")->where("order_class=:order_class and city=:city and status = 'approve' and activity_id=:activity_id",
                        array(":order_class"=>$this->order_class,":city"=>$city,":activity_id"=>$this->activity_id))->queryScalar();
                if($rows > 0){
                    $message = Yii::t('procurement',"Multiple orders are not allowed simultaneously")."，请联系老总放行";
                    $this->addError($attribute,$message);
                    return false;
                }
            }else{
                $this->acc_bool = true;
            }
        }

        if(!empty($this->activity_id) &&!empty($this->order_class) && $this->order_class != "Fast"){
            $nowDate = date("Y-m-d");
            $activityList = true;
            $city = Yii::app()->user->city();
            if($this->order_class == "Import"||$this->order_class == "Domestic"){
                $rows = Yii::app()->db->createCommand()->select("id")
                    ->from("opr_order")
                    ->where('activity_id = :activity_id and judge=1 and order_class=:order_class and city=:city and status!="pending" and id !=:id',
                        array(':activity_id'=>$this->activity_id,':city'=>$city,':order_class'=>$this->order_class,':id'=>$this->id))->queryAll();
                $num = $rows?count($rows):0;
                $rs = Yii::app()->db->createCommand()->select()->from("opr_order_activity")
                    ->where('id=:id and start_time<=:date and end_time>=:date and num>:num and order_class=:order_class',
                        array(':id'=>$this->activity_id,':date'=>$nowDate,':num'=>$num,':order_class'=>$this->order_class))->queryAll();
                if(!$rs){
                    $message = Yii::t('procurement',$this->order_class).Yii::t('procurement',' Order for Over time Or Order Number Quantity over limit');
                    $this->addError($attribute,$message);
                }
            }else{
                $message = Yii::t('procurement',$this->order_class).Yii::t('procurement','Error:Not Font Order Class');
                $this->addError($attribute,$message);
            }
        }elseif ($this->activity_id == 0 && $this->order_class != "Fast"){
            $message = Yii::t('procurement',"Order of Activity").Yii::t('procurement',' Not Null');
            $this->addError($attribute,$message);
        }
    }

    //驗證是否正常進入
    public function validateLogin(){
        if($this->activity_id == 0){
            $this->order_class = "Fast";
            return true;
        }
        if (is_numeric($this->activity_id)){
            $rs = Yii::app()->db->createCommand()->select("order_class")
                ->from("opr_order_activity")->where('start_time <= :date AND end_time >=:date AND id =:id',
                    array(':date'=>date("Y-m-d"),":id"=>$this->activity_id))->queryAll();
            if($rs){
                $this->order_class = $rs[0]["order_class"];
                return true;
            }
        }
        return false;
    }

    //根據訂單id查訂單所有狀態
    public static function getStatusListToId($order_id){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_order_status")->where('order_id=:order_id',array(':order_id'=>$order_id))->queryAll();
        return $rs;
    }

    //根據訂單id查訂單所有物品
    public static function getGoodsListToId($order_id,$city=''){
        $arr=array();
        $order_class=Yii::app()->db->createCommand()->select("order_class")->from("opr_order")->where("id=:id",array(":id"=>$order_id))->queryAll();
        $order_class=$order_class[0]["order_class"];
        $rows=Yii::app()->db->createCommand()->select()->from("opr_order_goods")->where("order_id=:order_id",array(":order_id"=>$order_id))->queryAll();
        foreach ($rows as $row){
            $goods = OrderForm::getOneGoodsToId($row["goods_id"],$order_class,$city);
            if(empty($goods)){
                continue;
            }
            $list = array(
                "id"=>$row["id"],
                "goods_id"=>$row["goods_id"],
                "goods_code"=>$goods["goods_code"],
                "name"=>$goods["name"],
                "type"=>$goods["type"],
                "unit"=>$goods["unit"],
                "price"=>$goods["price"],//rules_id
                "multiple"=>empty($goods["rules_id"])?$goods["multiple"]:RulesForm::getRulesToId($goods["rules_id"])["multiple"],
                "classify_id"=>$goods["classify_id"],
                "stickies_id"=>empty($goods["stickies_id"])?"":$goods["stickies_id"]
            );
            if(!empty($goods["net_weight"])){
                $list["net_weight"] = $goods["net_weight"];
            }
            if(!empty($goods["gross_weight"])){
                $list["gross_weight"] = $goods["gross_weight"];
            }
            if(!empty($goods["len"])){
                $list["LWH"] = $goods["len"]."×".$goods["width"]."×".$goods["height"];
            }
            $list["goods_num"] = $row["goods_num"];

            $list["confirm_num"] = (empty($row["confirm_num"]) && $row['confirm_num'] !== "0")?$row["goods_num"]:$row["confirm_num"];
            $list["note"] = $row["note"];
            $list["remark"] = $row["remark"];
            array_push($arr,$list);
        }
        return $arr;
    }


    //獲取物品列表
    public function getGoodsList($order_class=0,$city=''){
        $city = empty($city)?Yii::app()->user->city():$city;
        $bool = false;
        switch ($order_class){
            case "Import":
                $from = "opr_goods_im";
                $row = Yii::app()->db->createCommand()->select("price_type")->from("opr_city_price")
                    ->where("city=:city",array(":city"=>$city))->queryRow();
                $bool = ($row && $row["price_type"] == 2)?true:false;
                break;
            case "Domestic":
                $from = "opr_goods_do";
                break;
            case "Fast":
                $from = "opr_goods_fa";
                break;
        }
        $rs = Yii::app()->db->createCommand()->select()->from($from)->queryAll();
        foreach ($rs as &$r){
            if($bool){
                $r["price"]=$r["price_two"];
            }
            $r["img_url"]=empty($r["img_url"])?"":Yii::app()->request->baseUrl."/".$r["img_url"];
        }
        return $rs;
    }

    //獲取單個物品
    public function getOneGoodsToId($goods_id,$order_class=0,$city=''){
        switch ($order_class){
            case "Import":
                $from = "opr_goods_im";
                break;
            case "Domestic":
                $from = "opr_goods_do";
                break;
            case "Fast":
                $from = "opr_goods_fa";
                break;
        }
        $rs = Yii::app()->db->createCommand()->select()->from($from)->where("id=:id",array(":id"=>$goods_id))->queryRow();
        if($rs){
            if($from == "opr_goods_im"&&!empty($city)){ //进口货的价格需要判定
                $row = Yii::app()->db->createCommand()->select("price_type")->from("opr_city_price")->where("city=:city",array(":city"=>$city))->queryRow();
                $rs["price"] = ($row && $row["price_type"] == 2)?$rs["price_two"]:$rs["price"];
            }
            return $rs;
        }else{
            return array();
        }
    }

    //獲取單個物品
    public function getHeadHtml(){
        $html = "";
        switch ($this->order_class){
            case "Import":
                $html = '<div class="text-danger"><p><strong>注意事項：	</strong></p>';
$html.='<p>甲：	特許經營商知悉因特許經營商並無責任必須定期購貨，故此供應商不可能每天備存大量存貨等待不一定出現的訂單，因而需要時間備貨。亦知悉自己須保持合理庫存及定期盤點存貨的重要性，以便及早發現存貨不足，提早訂貨</p>';
                $html.='<p>乙：	備貨貨期：	由嘉富貨倉提取之貨物	 7天（由收到訂貨申請表後計）。只能以自提或速遞送貨，運費昂貴，亦沒有發票	';
                $html.='需報關上貨之貨物 	 25天到達深圳（收到訂貨申請表後計，含報關及運貨時間），未包括由深圳到目的地之運貨時間。國內交貨之貨物	 14天（收到貨款後計），未包運貨時間。</p>';
$html.='<p>丙：	貨品來源自美國或/台灣或/馬來西亞，單價含來源地至香港運費。</p>';
$html.='<p>丁：	不論來源地，單價為嘉富貨倉提取價（不包括嘉富到目的地運費）、或國內出廠價（不包括國內運費）。</p></div>';

                break;
            case "Domestic":
                $html = '<div class="text-danger"><p><strong>注意事項：	</strong></p>';
                $html.='<p>訂貨週期：每月一次</p>';
                $html.='<p>訂貨時間：每月20號 (請於20號前向總公司提供訂貨單，如遇假日將順延一個工作天)</p></div>';
                break;
            case "Fast":
                $html = '<div class="text-danger"><p><strong>注意事項：	</strong></p>';
                $html.='<p>甲：	特許經營商知悉因特許經營商並無責任必須定期購貨，故此供應商不可能每天備存大量存貨等待不一定出現的訂單，因而需要時間備貨。亦知悉自己須保持合理庫存及定期盤點存貨的重要性，以便及早發現存貨不足，提早訂貨	</p>';
                $html.='<p>乙：	備貨貨期：	由嘉富貨倉提取之貨物	 7天（由收到訂貨申請表後計）。只能以自提或速遞送貨，運費昂貴，亦沒有發票							
		需報關上貨之貨物 	 20天到達深圳（收到訂貨申請表後計，含報關及運貨時間），未包括由深圳到目的地之運貨時間。							
		國內交貨之貨物	 14天（收到貨款後計），未包運貨時間。</p>';
                $html.='<p>丙：	貨品來源自美國或/台灣或/馬來西亞，單價含來源地至香港運費。</p>';
                $html.='<p>丁：	不論來源地，單價為嘉富貨倉提取價（不包括嘉富到目的地運費）、或國內出廠價（不包括國內運費）。</p></div>';
                break;
        }
        return $html;
    }

    //根據當前時間點輸出可用活動
    public function getActivityToNow(){
        $arr[$this->activity_id] = OrderList::getActivityTitleToId($this->activity_id);
        $rows = Yii::app()->db->createCommand()->select("id,activity_title")->from("opr_order_activity")
            ->where('start_time <=:date and end_time >=:date and id != :id',
                array(':date'=>date("Y-m-d"),':id'=>$this->activity_id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row['id']] = $row['activity_title'];
            }
        }
        return $arr;
    }

    //獲取所有用戶列表
    public function getUserListArr(){
        $arr=array(""=>"");
        $suffix = Yii::app()->params['envSuffix'];
        $table = "security".$suffix.".sec_user";
        $rs = Yii::app()->db->createCommand()->select("username")->from($table)->queryAll();
        foreach ($rs as $row){
            $arr[$row["username"]] = $row["username"];
        }
        return $arr;
    }

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order")->where("id=:id and judge=1",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->order_code = $row['order_code'];
                $this->order_class = $row['order_class'];
                $this->activity_id = $row['activity_id'];
                $this->goods_list = $this->getGoodsListToId($row['id'],$row["city"]);
                $this->order_user = $row['order_user'];
                //$this->technician = $row['technician'];
                $this->status = $row['status'];
                $this->remark = $row['remark'];
                $this->ject_remark = $row['ject_remark'];
                $this->fish_remark = $row['fish_remark'];
                $this->statusList = $this->getStatusListToId($row['id']);
                break;
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
        $oldOrderStatus = Yii::app()->db->createCommand()->select()->from("opr_order")
            ->where("id=:id",array(":id"=>$this->id))->queryAll();
		$sql = '';
        $goodsBool = true;
        $insetBool = false;
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from opr_order where id = :id and judge=1";
                $goodsBool = false;
                break;
            case 'new':
                $insetBool = true;
                $sql = "insert into opr_order(
							order_user, order_class, city, activity_id, remark, status, lcu, lcd
						) values (
							:order_user,:order_class,:city,:activity_id,:remark, :status, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_order set
							order_class = :order_class,
							activity_id = :activity_id,
							remark = :remark,
							luu = :luu,
							lud = :lud
						where id = :id and judge=1
						";
                break;
            case 'audit':
                if(empty($this->id)){
                    $insetBool = true;
                    $sql = "insert into opr_order(
							order_user, order_class, activity_id, city, remark, status, lcu, lcd
						) values (
							:order_user,:order_class,:activity_id,:city,:remark, :status, :lcu, :lcd
						)";
                }else{
                    $sql = "update opr_order set
							order_class = :order_class,
							activity_id = :activity_id,
							remark = :remark,
							ject_remark = '',
							status_type = 0,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id and judge=1
						";
                }
                break;
            case 'finish':
                $sql = "update opr_order set
							remark = :remark,
							fish_remark = :fish_remark,
							status_type = 1,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id and judge=1
						";
                $goodsBool = false;
                $oldOrderStatus[0]["status_type"] = 1;
                break;
            default:
                $goodsBool = false;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $order_username = Yii::app()->user->name;
        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':order_user')!==false)
            $command->bindParam(':order_user',$order_username,PDO::PARAM_STR);
        if (strpos($sql,':order_class')!==false)
            $command->bindParam(':order_class',$this->order_class,PDO::PARAM_STR);
        if (strpos($sql,':activity_id')!==false)
            $command->bindParam(':activity_id',$this->activity_id,PDO::PARAM_STR);
        if (strpos($sql,':status')!==false){
            if($this->scenario == "new"){
                $this->status = "pending";
            }elseif ($this->scenario == "audit"){
                $this->status = "sent";
            }elseif ($this->scenario == "finish"){
                $this->status = "finished";
            }
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        }

        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':fish_remark')!==false)
            $command->bindParam(':fish_remark',$this->fish_remark,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        $command->execute();

        if ($insetBool){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
            $code = strval($this->id);
            $this->order_code = "";
            for($i = 0;$i < 5-strlen($code);$i++){
                $this->order_code.="0";
            }
            $this->order_code = date("Ym").$this->order_code.$code;
            Yii::app()->db->createCommand()->update('opr_order', array(
                'order_code'=>$this->order_code,
                'judge'=>1,
                'city'=>$city,
                'lcu_email'=>Yii::app()->user->email(),
            ), 'id=:id', array(':id'=>$this->id));
        }
        if ($this->scenario=='delete'){
            Yii::app()->db->createCommand()->delete('opr_order_status', 'order_id=:order_id', array(':order_id'=>$this->id));
            Yii::app()->db->createCommand()->delete('opr_order_goods', 'order_id=:order_id', array(':order_id'=>$this->id));
        }else{
            Yii::app()->db->createCommand()->insert('opr_order_status', array(
                'order_id'=>$this->id,
                'status'=>$this->status,
                'r_remark'=>$this->remark,
                'lcu'=>Yii::app()->user->user_display_name(),
                'time'=>date('Y-m-d H:i:s'),
            ));
        }


        if ($goodsBool){
            //物品的添加、修改
            foreach ($this->goods_list as $goods){
                if(empty($goods["id"])){
                    //添加
                    Yii::app()->db->createCommand()->insert('opr_order_goods', array(
                        'goods_id'=>$goods["goods_id"],
                        'order_id'=>$this->id,
                        'goods_num'=>$goods["goods_num"],
                        'note'=>$goods["note"],
                        'city'=>$city,
                        'lcu'=>Yii::app()->user->user_display_name(),
                    ));
                }else{
                    //修改
                    Yii::app()->db->createCommand()->update('opr_order_goods', array(
                        'goods_id'=>$goods["goods_id"],
                        'goods_num'=>$goods["goods_num"],
                        'note'=>$goods["note"],
                        'luu'=>Yii::app()->user->user_display_name(),
                    ), 'id=:id', array(':id'=>$goods["id"]));
                }
            }
        }
        if($this->acc_bool){ //終止一次性放行
            if($this->order_class == "Domestic"){
                Yii::app()->db->createCommand()->update('opr_order_acc', array(
                    'acc_do'=>0
                ), 'city=:city', array(':city'=>$city));
            }elseif ($this->order_class == "Import"){
                Yii::app()->db->createCommand()->update('opr_order_acc', array(
                    'acc_im'=>0
                ), 'city=:city', array(':city'=>$city));
            }
        }

        $this->updateGoodsStatus();
        //發送郵件
        OrderGoods::sendEmail($oldOrderStatus,$this->status,$this->order_code,$this->activity_id);
		return true;
	}

    //修改訂單內物品的狀態
    protected function updateGoodsStatus(){
        Yii::app()->db->createCommand()->update('opr_order_goods', array(
            'order_status'=>$this->status,
        ), 'order_id=:order_id', array(':order_id'=>$this->id));
    }
}
