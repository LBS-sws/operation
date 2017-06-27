<?php

class OrderList extends CListPageModel
{
    public $activity_title;
	public function attributeLabels()
	{
		return array(	
			'order_code'=>Yii::t('procurement','Order Code'),
			'order_class'=>Yii::t('procurement','Order Class'),
			'activity'=>Yii::t('procurement','Activity Code'),
			'goods_list'=>Yii::t('procurement','Goods List'),
			'order_user'=>Yii::t('procurement','Order User'),
			'technician'=>Yii::t('procurement','Technician'),
			'activity_id'=>Yii::t('procurement','Order of Activity'),
			'status'=>Yii::t('procurement','Order Status'),
			'city'=>Yii::t('procurement','Order For City'),
			'lcd'=>Yii::t('procurement','Apply for time'),
		);
	}

    //根據活動id查活動編號
    public function getActivityTitleToId($activity_id){
        $rs = Yii::app()->db->createCommand()->select("activity_title")
            ->from("opr_order_activity")->where('id=:id',array(':id'=>$activity_id))->queryAll();
        if($rs){
            return $rs[0]["activity_title"];
        }
        return "";
    }

	public function retrieveDataByPage($pageNum=1,$activity_id="yes")
	{
	    //order_user = '$userName' OR technician = '$userName'
		$city = Yii::app()->user->city();
		$userName = Yii::app()->user->name;
		if($activity_id === "yes"){
            $sql1 = "select *
				from opr_order
				where (city = '$city' AND judge=1) 
			";
            $sql2 = "select count(id)
				from opr_order
				where (city = '$city' AND judge=1) 
			";
        }else{
		    $this->activity_title = $this->getActivityTitleToId($activity_id);
            $sql1 = "select *
				from opr_order
				where (activity_id = '$activity_id' AND judge=1 AND status != 'pending' AND status != 'cancelled') 
			";
            $sql2 = "select count(id)
				from opr_order
				where (activity_id = '$activity_id' AND judge=1 AND status != 'pending' AND status != 'cancelled') 
			";
        }
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'lcd':
					$clause .= General::getSqlConditionClause('lcd', $svalue);
				case 'order_code':
					$clause .= General::getSqlConditionClause('order_code', $svalue);
					break;
				case 'activity_id':
                    $activityIdSql = $this->getActivityIdSql($svalue);
                    $clause .= " and activity_id in ($activityIdSql)";
					break;
				case 'city':
				    $citySql = $this->getCitySql($svalue);
					$clause .= " and city in ($citySql)";
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by id desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
					$this->attr[] = array(
						'id'=>$record['id'],
                        'order_code'=>$record['order_code'],
                        'order_class'=>Yii::t("procurement",$record['order_class']),
                        'activity_id'=>$this->getActivityTitleToId($record['activity_id']),
						'goods_list'=>OrderForm::getGoodsListToId($record['id']),
						'order_user'=>$record['order_user'],
						'technician'=>$record['technician'],
						'status'=>$record['status'],
						'city'=>$record['city'],
						'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
					);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_ya01'] = $this->getCriteria();
		return true;
	}

	public function printOrderStatus($status){
        switch ($status){
            case "pending":
                //草稿，未发送
                return Yii::t("procurement","Draft, not sent");
            case "sent":
                //已发送，待审核
                return Yii::t("procurement","Sent, pending approval");
            case "read":
                return Yii::t("procurement","Have read,Drop shipping");
            case "approve":
                //已发货，待收货
                return Yii::t("procurement","Shipped out, Wait for receiving");
            case "reject":
                return Yii::t("procurement","Reject");
            case "finished":
                return Yii::t("procurement","finished");
            default:
                return Yii::t("procurement","Error Status");
        }
    }

	public function printPurchaseStatus($status){
        switch ($status){
            case "sent":
                //已发送，待审核
                return Yii::t("procurement","pending approval");
            case "read":
                return Yii::t("procurement","Have read,Drop shipping");
            case "approve":
                //已审核，已发货
                return Yii::t("procurement","Has been approved, Shipped out");
            case "reject":
                return Yii::t("procurement","Reject");
            case "finished":
                return Yii::t("procurement","finished");
            default:
                return Yii::t("procurement","Error Status");
        }
    }

    public function getCitySql($str){
        $citySql = "";
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix.".sec_city";
        $rs = Yii::app()->db->createCommand()->select("code")->from($suffix)->where(array('like', 'name', "%$str%"))->queryAll();

        if($rs){
            foreach ($rs as $key => $row){
                if($key != 0){
                    $citySql.=",";
                }
                $citySql.="'".$row["code"]."'";
            }
        }else{
            $citySql.="''";
        }
	    return $citySql;
    }

    public function getCityNameToCode($code){
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix.".sec_city";
        $rs = Yii::app()->db->createCommand()->select("name")->from($suffix)->where("code=:code",array(":code"=>$code))->queryAll();

        if($rs){
            return $rs[0]["name"];
        }else{
            return "";
        }
    }

    public function getActivityIdSql($str){
        $activityIdSql = "";
        $rs = Yii::app()->db->createCommand()->select("id")->from("opr_order_activity")->where(array('like', 'activity_title', "%$str%"))->queryAll();

        if($rs){
            foreach ($rs as $key => $row){
                if($key != 0){
                    $activityIdSql.=",";
                }
                $activityIdSql.="'".$row["id"]."'";
            }
        }else{
            $activityIdSql.="''";
        }
	    return $activityIdSql;
    }
}
