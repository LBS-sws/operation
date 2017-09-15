<?php

class MyExcelTwo {
	protected $objPHPExcel;
	protected $objActSheet;
	protected $listArr=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
	protected $row = 1;

	public function __construct() {
		$phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
		spl_autoload_unregister(array('YiiBase','autoload'));
		include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->objPHPExcel = new PHPExcel();
        $this->objPHPExcel->getProperties()
            ->setCreator("WOLF")
            ->setLastModifiedBy("WOLF")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
        $this->objActSheet = $this->objPHPExcel->setActiveSheetIndex(0); //填充表头
	}

	//設置起始行
	public function setStartRow($num){
	    $this->row = $num;
    }

	//設置某行的內容
	public function setRowContent($row,$str,$endRow=0){
        $this->objActSheet->setCellValue($row,$str);
        if(!empty($endRow)){
            $this->objActSheet->mergeCells($row.":".$endRow);
        }
    }

    //設置規則提示
    public function setRulesArr($arr){
        for ($i = 0;$i<count($arr);$i++){
            $this->objActSheet->setCellValue("A".($i+1),$arr[$i]);
            $this->objActSheet->getStyle( "A".($i+1))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);;
        }
    }

	//設置表頭
	public function setDataHeard($heardArr,$title="訂單表"){
        $this->objPHPExcel->getActiveSheet()->setTitle($title);
        //3.填充表格
        $i = 0;
        foreach ($heardArr as $item){
            $this->objActSheet->setCellValue($this->listArr[$i].$this->row,$item);
            $i++;
        }
    }

	//設置内容
	public function setDataBody($bodyArr){
        //填充内容
        foreach ($bodyArr as $list){
            $this->row++;
            $i = 0;
            foreach ($list as $item){
                $this->objActSheet->setCellValue($this->listArr[$i].$this->row,$item);
                $i++;
            }
        }
    }

    //輸出excel表格
    public function outDownExcel($fileName){
        ob_end_clean();//清除缓冲区,避免乱码
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header('Content-Disposition: attachment;filename='.$fileName);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel,'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}
?>