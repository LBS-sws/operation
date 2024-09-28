<?php
//2024年9月28日09:28:46

class CurlNotesList extends CListPageModel
{
    public $info_type;

    public function rules()
    {
        return array(
            array('info_type,attr, pageNum, noOfItem, totalRow,city, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }

    public function getCriteria() {
        return array(
            'info_type'=>$this->info_type,
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'dateRangeValue'=>$this->dateRangeValue,
        );
    }
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'status_type'=>Yii::t('curl','status type'),
			'info_type'=>Yii::t('curl','info type'),
			'info_url'=>Yii::t('curl','info url'),
			'data_content'=>Yii::t('curl','data content'),
			'out_content'=>Yii::t('curl','out content'),
			'message'=>Yii::t('curl','message'),
			'lcu'=>Yii::t('curl','lcu'),
			'lcd'=>Yii::t('curl','lcd'),
			'lud'=>Yii::t('curl','lud'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select * 
				from opr_api_curl 
				where 1=1 
			";
		$sql2 = "select count(id)
				from opr_api_curl 
				where 1=1 
			";
		$clause = "";
        if(!empty($this->info_type)){
            $svalue = str_replace("'","\'",$this->info_type);
            $clause.=" and info_type='$svalue' ";
        }
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'status_type':
					$clause .= General::getSqlConditionClause('status_type',$svalue);
					break;
				case 'info_type':
					$clause .= General::getSqlConditionClause('info_type',$svalue);
					break;
				case 'info_url':
					$clause .= General::getSqlConditionClause('info_url',$svalue);
					break;
				case 'data_content':
					$clause .= General::getSqlConditionClause('data_content',$svalue);
					break;
				case 'out_content':
					$clause .= General::getSqlConditionClause('out_content',$svalue);
					break;
				case 'message':
					$clause .= General::getSqlConditionClause('message',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by id desc ";
        }

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
						'status_type'=>self::getCurlStatusNameToID($record['status_type']),
						'info_type'=>self::getInfoTypeList($record['info_type'],true),
						'info_url'=>$record['info_url'],
						'data_content'=>$record['data_content'],
						'out_content'=>$record['out_content'],
						'message'=>$record['message'],
						'lcu'=>$record['lcu'],
						'lcd'=>$record['lcd'],
						'lud'=>$record['lud'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['opr_curlNotes_c01'] = $this->getCriteria();
		return true;
	}

    //获取员工类型翻译
    public static function getCurlStatusNameToID($id){
        $id = "".$id;
        $list = array(
            "P"=>"未进行",
            "C"=>"已完成",
            "E"=>"错误",
        );
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }

    //翻译curl的类型
    public static function getInfoTypeList($key="",$bool=false){
        $list = array(
            "warehouse"=>"仓库",
            "delivery"=>"外勤领料",
            "expenseAudit"=>"日常费用报销",
            "remitAudit"=>"日常付款",
            "temporaryAudit"=>"暂支单",
            "customer"=>"客户资料",
            "customerAll"=>"批量客户资料",
        );
        if($bool){
            if(key_exists($key,$list)){
                return $list[$key];
            }else{
                return $key;
            }
        }else{
            return $list;
        }
    }

	public function sendID($index){
        $row = Yii::app()->db->createCommand()->select("*")->from("opr_api_curl")
            ->where("id=:id", array(':id'=>$index))->queryRow();
        if($row){
            $rtn = CurlForJD::sendUpdateRowForJD($row);
            if(in_array($row["info_type"],array("customer","customerAll"))){
                CurlNotesList::saveJDCustomerID($rtn);
            }
            return true;
        }else{
            return false;
        }
    }

    public static function saveJDCustomerID($rtn){
        if($rtn['code']==200){//成功
            $suffix = Yii::app()->params['envSuffix'];
            $jsonList = json_decode($rtn['outData'],true);
            if(is_array($jsonList)&&isset($jsonList["data"]["result"])){
                foreach ($jsonList["data"]["result"] as $row){
                    if(key_exists("billStatus",$row)&&$row["billStatus"]===true){
                        $jdID = $row["id"];
                        $lbsID = isset($row["keys"]["lbs_apikey"])?$row["keys"]["lbs_apikey"]:0;
                        $rs = Yii::app()->db->createCommand()->select("id,field_value")->from("swoper{$suffix}.swo_send_set_jd")
                            ->where("set_type ='customer' and table_id=:table_id and field_id=:field_id",array(
                                ':field_id'=>'jd_customer_id',':table_id'=>$lbsID,
                            ))->queryRow();
                        if($rs){
                            if(empty($rs["field_value"])){//空值才允许修改
                                Yii::app()->db->createCommand()->update("swoper{$suffix}.swo_send_set_jd",array(
                                    "field_value"=>$jdID,
                                ),"id=:id",array(':id'=>$rs["id"]));
                            }
                        }else{
                            Yii::app()->db->createCommand()->insert("swoper{$suffix}.swo_send_set_jd",array(
                                "table_id"=>$lbsID,
                                "set_type"=>'customer',
                                "field_id"=>'jd_customer_id',
                                "field_value"=>$jdID,
                            ));
                        }
                    }
                }
            }
        }
    }

	public function sendCurlForIDAndType($id,$type,$info_type){
	    switch ($info_type){
            case "warehouse"://仓库
                if($type=="add"){
                    CurlForWareHouse::addGood($id);
                }else{
                    CurlForWareHouse::editGood($id);
                }
                echo "warehouse success ! id:{$id},Scenario:{$type}";
                break;
            case "addGoodAll":
                CurlForWareHouse::addGoodAll();
                echo "addGoodAll success !";
                break;
            case "addGoodForCity":
                $city = key_exists("city",$_GET)?$_GET["city"]:Yii::app()->user->city();
                CurlForWareHouse::addGoodForCity($city);
                echo "addGoodAll success ! city:{$city}";
                break;
        }
    }

    public static function getCurlTextForID($id,$type=0){
        $type = "".$type;
        $list = array(
            0=>"data_content",//请求内容
            1=>"out_content",//响应的内容
            //2=>"cmd_content",//执行结果
        );
        $selectStr = key_exists($type,$list)?$list[$type]:$list[0];
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select($selectStr)->from("opr_api_curl")
            ->where("id=:id", array(':id'=>$id))->queryRow();
        if($row){
            $searchList = array("\\r","\\n","\\t");
            $replaceList = array("\r","\n","\t");
            return str_replace($searchList,$replaceList,$row[$selectStr]);
        }else{
            return "";
        }
    }
}
