<?php

class RankingHalfForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $rank;//排名
	public $name;
	public $employee_id;
	public $employee_code;
	public $employee_name;
	public $city;
	public $rank_year;
	public $rank_month;
	public $score_sum;
	public $other_score;

	public $startDate;
	public $endDate;

	public $arrList;//数据列表

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'rank'=>Yii::t('rank','rank'),
            'code'=>Yii::t('rank','employee code'),
            'name'=>Yii::t('rank','employee name'),
            'employee_id'=>Yii::t('rank','employee name'),
            'city_name'=>Yii::t('rank','city'),
            'rank_month'=>Yii::t('rank','month'),
            'rank_year'=>Yii::t('rank','year'),
            'score_sum'=>Yii::t('rank','Score Sum'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,employee_id,rank_month,rank_year','safe'),
		);
	}

	public function retrieveData($index,$rank=0,$year=2022,$month=1)
	{
        $index = is_numeric($index)?$index:0;
        $year = is_numeric($year)?$year:2022;
        $month = is_numeric($month)?$month:1;
        $suffix = Yii::app()->params['envSuffix'];
        $searchSql= " a.employee_id='{$index}' and a.rank_year='{$year}' ";
        $monthScope =RankingHalfList::quarterHalf($month);
        $searchSql.=" and a.rank_month in ({$monthScope}) ";
        $selectSql="";
        foreach (RankingMonthForm::$sqlDate as $item=>$rule){
            if($rule["add"]){
                $selectSql.=",sum(a.{$item}) as {$item}";
            }else{
                $selectSql.=",max(a.{$item}) as {$item}";
            }
        }
        $row=Yii::app()->db->createCommand()
            ->select("a.employee_id,sum(a.other_score) as other_score,b.city as employee_city,b.code,b.name,f.name as city_name{$selectSql}")
            ->from("opr_technician_rank a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("security$suffix.sec_city f","b.city = f.code")
            ->where("$searchSql")->group("a.employee_id,b.code,b.name,f.name")->queryRow();
        if ($row) {
            $this->arrList=array();
			$this->id = $row['employee_id'];
			$this->rank = $rank;
			$this->name = $row['name']."({$row['code']})";
			$this->employee_id = $row['employee_id'];
			$this->employee_code = $row['code'];
			$this->employee_name = $row['name'];
            $this->city = $row['employee_city'];
			$this->rank_year = $year;
			$this->rank_month = $month;
			$this->score_sum = 0;
            $this->other_score = $row['other_score'];
			foreach (RankingMonthForm::$sqlDate as $item=>$rule){
                if($rule["reset"]){
                    $this->score_sum+= key_exists($item,$row)?floatval($row[$item]):0;
                }
                $this->arrList[$item] = key_exists($item,$row)?floatval($row[$item]):"";
            }
            return true;
		}else{
		    return false;
        }
	}
}