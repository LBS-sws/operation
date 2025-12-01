<?php
//2024年9月28日09:28:46

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
            $clause.=" and info_type='$svalue' ";
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
            "WarehouseFull"=>"批量修改仓库",
            "SupplierFull"=>"批量修改供应商",
            "PaymentFull"=>"回传报销单",
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
                "message"=>null,
                "lcu"=>$uid,
            ),"id={$index}");
            return true;
        }else{
            return false;
        }
    }

    private function warehouseData($num){
        return array(
            "timestamp"=>"2024-05-24 10:33:28",//记录时间
            "good_code"=>"JDGD0000{$num}",//LBS的物料编号 = 金蝶物料编号。
            "good_name"=>"帽子-{$num}",//物料名称
            "classify_no"=>"JDCY0001",//分类编号
            "unit"=>"个",//物料单位
            "decimal_num"=>null,//是否允許小數
            "matching"=>null,//產品配比
            "matters"=>null,//注意事項
            "jd_username"=>"800002",//操作人员
            "display"=>$num%2==0?1:0,//是否顯示 1：顯示  0：不顯示

            "jd_good_id"=>$num,//金蝶物料id
            "jd_classify_no"=>"JDCY0001",//分类编号
            "jd_classify_name"=>"帽子",//分类编号
            "jd_unit_code"=>"UN01",//物料单位编号
            "data_type"=>$num%2==0?1:2,//数据类型：1：存量(LBS的旧数据) 2：新数据
            "old_good_no"=>"W0001{$num}",//老版LBS物料编号
        );
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

    public function getGoods($city,$goods){
	    $data =array("city_arr"=>$city,"jd_good_arr"=>$goods);
	    $this->sendCurl("/JDSync/getGoods",$data);
    }

    public function getSupplier($city){
	    $data =array("city_arr"=>$city);
	    $this->sendCurl("/JDSync/getSupplier",$data);
    }

    public function testPayment($index){
	    $data =array(
            "cut_account_no"=>"98990078801400002771",
            "cut_money"=>"333.000000",
            "jd_username"=>"KD3",
            "lbs_id"=>"19",
            "payment_date"=>"2024/07/29",
            "payment_type"=>"BANKOUT",
            "state_type"=>"1",
            "timestamp"=>"2024-07-31 00=>57=>21"
        );
	    $this->sendCurl("/JDSync/paymentFull",array($data));
    }

    public function TestSupplier(){
        $data = array(
            array(
                "timestamp"=>"2024-06-26 09:51:46",
                "jd_supplier_id"=>"12",
                "jd_condition_code"=>"code001",
                "jd_condition_name"=>"条件1",
                "lbs_id"=>"2",
                "supplier_code"=>"UP0001",
                "supplier_name"=>"api修改供应商",
                "full_name"=>"api修改供应商全称",
                "tax_reg_no"=>"TAX00001",
                "cont_name"=>"api修改供应商联系人",
                "cont_phone"=>"api修改供供应商电话",
                "address"=>"api修改供应商地址",
                "bank_name"=>"api修改付款账户",
                "bank_code"=>"BANK00001",
                "data_type"=>"1",//数据类型：1：存量(LBS的旧数据) 2：新数据
            ),
            array(
                "timestamp"=>"2024-06-26 11:51:46",
                "jd_supplier_id"=>"13",
                "jd_condition_code"=>"code002",
                "jd_condition_name"=>"条件2",
                "lbs_id"=>"2",
                "supplier_code"=>"UP0003",
                "supplier_name"=>"api修改供应商3",
                "full_name"=>"api修改供应商全称3",
                "tax_reg_no"=>"TAX00003",
                "cont_name"=>"api修改供应商联系人3",
                "cont_phone"=>"api修改供供应商电话3",
                "address"=>"api修改供应商地址3",
                "bank_name"=>"api修改付款账户3",
                "bank_code"=>"BANK00003",
                "data_type"=>"2",//数据类型：1：存量(LBS的旧数据) 2：新数据
            ),
        );
        $this->sendCurl("/JDSync/UpdateSupplierFull",$data);
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
        echo  "url:<br/>".$url."<br/><br/>";
        echo  "data:<br/>".$data."<br/><br/>";
        echo  "key:<br/>".$svrkey."<br/><br/>";
        echo  "<br/>";
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
        $secret_key = '0fdc8906eda40a5b3d02c8ef6ad0aab5'; // 加密密钥

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

    public static function encryptSalt($password) {
        // 使用'$6$'指定SHA-256和自动盐值生成的前缀
        // 这里的12个字符是盐值的长度，可以根据需要调整
        $salt = '$6$' . substr(sha1(uniqid(mt_rand(), true)), 0, 12);
        return crypt($password, $salt);
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
        $row = Yii::app()->db->createCommand()->select($selectStr)->from("datasync{$suffix}.sync_jd_api_curl")
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
