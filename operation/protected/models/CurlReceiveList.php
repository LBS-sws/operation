<?php

class CurlReceiveList extends CListPageModel
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
				from datasync{$suffix}.sync_jd_api_curl 
				where 1=1 
			";
		$sql2 = "select count(id)
				from  datasync{$suffix}.sync_jd_api_curl  
				where 1=1 
			";
		$clause = "";
		if(!empty($this->info_type)){
            $svalue = str_replace("'","\'",$this->info_type);
            if($svalue=="Warehouse"){
                $clause.=" and info_type in ('Warehouse','WarehouseFull') ";
            }else{
                $clause.=" and info_type='$svalue' ";
            }
        }
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'id':
					$clause .= General::getSqlConditionClause('id',$svalue);
					break;
				case 'status_type':
					$clause .= General::getSqlConditionClause('status_type',$svalue);
					break;
				case 'info_type':
					$clause .= General::getSqlConditionClause('info_type',$svalue);
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
						'info_type'=>self::getInfoTypeList($record['info_type'],true),
						'status_type'=>$record['status_type'],
						'data_content'=>$record['data_content'],
						'out_content'=>$record['out_content'],
						//'data_content'=>urldecode($record['data_content']),
						//'out_content'=>urldecode($record['out_content']),
						'message'=>$record['message'],
						'lcu'=>$record['lcu'],
						'lcd'=>$record['lcd'],
						'lud'=>$record['lud'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['curlReceive_c01'] = $this->getCriteria();
		return true;
	}

	//翻译curl的类型
	public static function getInfoTypeList($key="",$bool=false){
        $list = array(
            "Warehouse"=>"仓库信息",
            "UpdateJDNO"=>"修改金蝶物料编号",
            //"T"=>"外勤领料",
        );
        if($bool){
            if(key_exists($key,$list)){
                return $list[$key];
            }else{
                return $key=="WarehouseFull"?"仓库信息":$key;
            }
        }else{
            return $list;
        }
    }

	public function sendID($index){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("datasync{$suffix}.sync_jd_api_curl")
            ->where("id=:id and status_type!='P'", array(':id'=>$index))->queryRow();
        if($row){
            Yii::app()->db->createCommand()->update("datasync{$suffix}.sync_jd_api_curl",array(
                "status_type"=>"P",
                "lcu"=>$uid,
            ),"id={$index}");
            return true;
        }else{
            return false;
        }
    }

    private function warehouseData($num){
        return array(
            "status"=>$num%3==0?"update":"add",//状态
            "city"=>"CD",//城市
            "timestamp"=>"2024-05-24 10:33:28",//记录时间
            "jd_warehouse_no"=>"CD001",//仓库编号(金蝶系统)
            "jd_good_no"=>"JDGD0000{$num}",//物料编号(金蝶系统)
            "good_name"=>"帽子-{$num}",//物料名称
            "classify_no"=>"JDCY0001",//分类编号
            "unit"=>"个",//物料单位
            "price"=>null,//单价
            "decimal_num"=>null,//是否允許小數
            "costing"=>null,//物料成本
            "inventory"=>78,//库存
            "min_num"=>60,//安全库存
            "matching"=>null,//產品配比
            "matters"=>null,//注意事項
            "jd_username"=>"800002",//操作人员
            "display"=>$num%2==0?1:0,//是否顯示 1：顯示  0：不顯示
        );
    }

    public function testWarehouseUpdate($no,$sum=100){
	    $data = array(
            "status"=>"update",//状态
            "city"=>"CD",//城市
            "timestamp"=>date_format(date_create(),"Y/m/d H:i:s"),//记录时间
            "jd_warehouse_no"=>"CD001",//仓库编号(金蝶系统)
            "jd_good_no"=>$no,//物料编号(金蝶系统)
            "good_name"=>"帽子-修改",//物料名称
            "classify_no"=>"JDCY0001",//分类编号
            "unit"=>"个",//物料单位
            "price"=>null,//单价
            "decimal_num"=>null,//是否允許小數
            "costing"=>null,//物料成本
            "inventory"=>$sum,//库存
            "min_num"=>70,//安全库存
            "matching"=>null,//產品配比
            "matters"=>null,//注意事項
            "jd_username"=>"800002",//操作人员
            "display"=>1,//是否顯示 1：顯示  0：不顯示
        );
	    $this->sendCurl("/JDSync/WarehouseOne",$data);
    }

    public function testWarehouseOne(){
        $data = self::warehouseData(1);
        $this->sendCurl("/JDSync/WarehouseOne",$data);
    }

    public function testUpdateJDNO(){
        $data = array(
            array("city"=>"HK","lbs_good_no"=>"W00001","jd_good_no"=>"jd00001"),
            array("city"=>"HK","lbs_good_no"=>"W00002","jd_good_no"=>"jd00002"),
            array("city"=>"HK","lbs_good_no"=>"W00003","jd_good_no"=>"jd00003"),
            array("city"=>"HK","lbs_good_no"=>"W00004","jd_good_no"=>"jd00004"),
        );
        $this->sendCurl("/JDSync/UpdateJDNO",$data);
    }

    public function testWarehouseFull($index){
        $index = is_numeric($index)?intval($index):10;
        $index = $index<5?5:$index;
	    $data =array();
	    for ($i=1;$i<=$index;$i++){
	        $data[]=self::warehouseData($i);
        }
	    $this->sendCurl("/JDSync/WarehouseFull",$data);
    }

    public function testIp(){
	    $data =array();
	    $this->sendCurl("/JDSync/ip",$data);
    }

    public function systemU($type){
        //$city = Yii::app()->user->city();
        $city = "CD";
        $list = array(
            ////获取发票内容
            "getData"=>array("args"=>array("city"=>"'{$city}'","start"=>"2023-01-01", "end"=>"2024-02-01", "customer"=>"")),
            //获取INV类型的详情
            "getInvDataDetail"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取INV类型的城市汇总
            "getInvDataCityAmount"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取INV类型的城市(月份)汇总
            "getInvDataCityMonth"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取INV类型的城市(周)汇总
            "getInvDataCityWeek"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取服务单月数据
            "getUServiceMoney"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取服务单月数据（月為鍵名)
            "getUServiceMoneyToMonth"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取服务单月数据（周為鍵名)
            "getUServiceMoneyToWeek"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取技术员金额（技术员编号為鍵名)
            "getTechnicianMoney"=>array("args"=>array("start"=>"2024-03-01","end"=>"2024-03-31","city"=>"'{$city}'")),
            //获取技术员金额U系统详情（需要自己分开服务单）
            "getTechnicianDetail"=>array("args"=>array("start"=>"2023-01-01","end"=>"2024-02-01","city"=>"'{$city}'")),
            //获取技术员的创新金额、夜单金额、服务金额
            "getTechnicianSNC"=>array("args"=>array("year"=>"2023","month"=>"2","city"=>"'{$city}'")),
        );
        if(key_exists($type,$list)){
            $params=array();
            if(!empty($list[$type]["args"])){
                foreach ($list[$type]["args"] as $item=>$value){
                    $params[$item] = key_exists($item,$_GET)?$_GET[$item]:"";
                    $params[$item] = !empty($params[$item])?$params[$item]:$value;
                }
            }
            $params["printBool"] = true;
            $func_name = "SystemU::".$type;
            $json = call_user_func_array($func_name, $params);
        }else{
            echo "404";
        }
    }

    private function sendCurl($url,$data){
        $data = json_encode($data);
        $url = Yii::app()->params['curlLink'].$url;
        $svrkey = self::generate_key();
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data),
            'Authorization: SvrKey '.$svrkey,
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if ($out===false) {
            echo 'Error: '.curl_error($ch);
        } else {
            var_dump($out);
        }
        curl_close($ch);
    }

    public static function generate_key(){
        $ip =Yii::app()->params['uCurlIP'];
        $interval = 600; // 10分钟的秒数
        $secret_key = 'c09c321acaf59c57e2a2a999e31b5ea8'; // 加密密钥

        //生成key
        $salt = floor(time() / $interval) * $interval; // 使用10分钟为间隔的时间戳作为盐

        $ip_split = explode('.', $ip);
        if(count($ip_split)!=4){
            return false;
        }
        $hexip = sprintf('%02x%02x%02x%02x', $ip_split[0], $ip_split[1], $ip_split[2], $ip_split[3]);
        $key = hash('sha256', $ip . $salt . $hexip);

        //加密发送时间戳
        $encryptedData = openssl_encrypt($salt, 'AES-128-ECB', $secret_key, OPENSSL_RAW_DATA);
        $encrypted = base64_encode($encryptedData);

        return $key.'.'.$encrypted;
    }
}
