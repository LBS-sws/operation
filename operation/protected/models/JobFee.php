<?php
class JobFee {
	public static function getData($year, $month) {
		$rtn = array();
		
		$key = Yii::app()->params['unitedKey'];
		$root = Yii::app()->params['unitedRootURL'];
		$url = $root.'/remote/getStaffJobFee.php';
		$data = array(
			"key"=>$key,
			"year"=>$year,
			"month"=>$month
		);
		$data_string = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:application/json',
			'Content-Length:'.strlen($data_string),
 		));
		$out = curl_exec($ch);
		if ($out===false) {
			$rtn = array(
				'code' => 0,
				'data' => array(),
				'msg' => curl_error($ch),
			);
		} else {
			$rtn = json_decode($out, true);
			$msg = self::getJsonError(json_last_error());
			if ($msg!='Success') {
				$rtn = array(
					'code' => 0,
					'data' => $out,
					'msg' => $msg,
				);
			}
		}
		
		return $rtn;
	}
	
	public static function getJsonError($error) {
		switch ($error) {
			case JSON_ERROR_NONE:
				return 'Success';
			case JSON_ERROR_DEPTH:
				return ' - Maximum stack depth exceeded';
			case JSON_ERROR_STATE_MISMATCH:
				return ' - Underflow or the modes mismatch';
			case JSON_ERROR_CTRL_CHAR:
				return ' - Unexpected control character found';
			case JSON_ERROR_SYNTAX:
				return ' - Syntax error, malformed JSON';
			case JSON_ERROR_UTF8:
				return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			default:
				return' - Unknown error ('.$error.')';
		}
	}
}
?>