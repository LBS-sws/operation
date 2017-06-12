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

}

?>