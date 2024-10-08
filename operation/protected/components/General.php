<?php
// Common Functions

class General extends CGeneral {

/* SAMPLE CODE	
// ===========
	public static function getAcctTypeList()
	{
		$list = array();
		$sql = "select id, acct_type_desc from acc_account_type order by acct_type_desc";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$list[$row['id']] = $row['acct_type_desc'];
			}
		}
		return $list;
	}
*/
	public function getUpdateDate() {
		$file = Yii::app()->basePath.'/config/lud.php';
		if (file_exists($file)) {
			$lud = require($file);
			return $lud;
		} else {
			return '2016/01/01';
		}
	}
	
    /*
     * 獲取必須測驗的測驗單id
     */
    public static function getQuizIdForMust(){
        $suffix = Yii::app()->params['envSuffix'];
        $quiz_id = Yii::app()->db->createCommand()
            ->select("id")->from("quiz$suffix.exa_quiz")
            ->order("join_must desc,id asc")->queryScalar();
        return $quiz_id?$quiz_id:0;
    }

    /*
     * 判斷系統位置
     * @return int  0：大陸。 1：台灣。2：新加坡。 3：吉隆坡
     */
    public static function SystemIsCN(){
        $suffix = Yii::app()->params['envSuffix'];
        $value = Yii::app()->db->createCommand()->select("set_value")
            ->from("hr$suffix.hr_setting")->where("set_name='systemId'")->queryScalar();
        return $value?$value:0;
    }

    /*
     * 加载日报表系统的SysBlock文件
     * @return str
     */
    public static function includeDrsSysBlock(){
        $systemList = require(Yii::app()->basePath.'/config/system.php');
        foreach ($systemList as $row){
            if($row["name"]=="Daily Report"){//读取日报表系统的公共文件
                $objName = end(explode("/",$row["webroot"]));
                $configPath = dirname(Yii::app()->basePath)."/../{$objName}/protected";
                include_once($configPath."/components/SysBlock.php");
                return true;
            }
        }
    }

    public static function getCityListWithCityAllow($city_allow='') {
        $list = array();
        $suffix = Yii::app()->params['envSuffix'];
        $clause = !empty($city_allow) ? "code in ($city_allow)" : "1>1";
        $sql = "select code, name from security$suffix.sec_city WHERE {$clause} order by name";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $list[$row['code']] = $row['name'];
            }
        }
        return $list;
    }

    public static function getCityNameForList($code) {
        if(empty($code)){
            return "";
        }
        if (self::isJSON($code)){
            $list = json_decode($code,true);
            $cityList = array();
            foreach ($list as $city){
                $cityList[]=self::getCityName($city);
            }
            return implode("、",$cityList);
        }elseif(strpos($code,",")!==false){
            $suffix = Yii::app()->params['envSuffix'];
            $sql = "select name from security$suffix.sec_city where code in ({$code})";
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $cityList = array_column($rows,"name");
            return implode("、",$cityList);
        }else{
            return self::getCityName($code);
        }
    }

    public static function getCityListForArea(){
        $suffix = Yii::app()->params['envSuffix'];
        //$sql = "select field_id, field_value from security$suffix.sec_template_info where temp_id=$id";
        $rows = Yii::app()->db->createCommand()->select("code,name")->from("security{$suffix}.sec_city")
            ->where("ka_bool=2")->queryAll();//0：城市 1：KA城市 2：区域
        $list = array();
        if($rows){
            foreach ($rows as $row){
                $city_allow = self::getMinCityForMaxCity($row["code"]);
                $cityStr = implode(",",array_keys($city_allow));

                $list[$row["code"]] = array("name"=>$row["name"],"city"=>$cityStr,"code"=>$row["code"]);
            }
        }
        return $list;
    }

    public static function getMinCityForMaxCity($city,$city_allow=array()){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("code,name,ka_bool")->from("security{$suffix}.sec_city")
            ->where("region=:region",array(":region"=>$city))->queryAll();//0：城市 1：KA城市 2：区域
        if($rows){
            foreach ($rows as $row){
                if(!key_exists($row["code"],$city_allow)){
                    $city_allow[$row["code"]]=$row;
                    $city_allow = self::getMinCityForMaxCity($row["code"],$city_allow);
                }
            }
        }
        return $city_allow;
    }
}

?>