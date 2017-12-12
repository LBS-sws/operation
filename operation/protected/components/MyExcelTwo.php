<?php

class MyExcelTwo {
    protected $objPHPExcel;
    protected $objActSheet;
    protected $listArr=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    protected $row = 1;
    protected $sheetNum = 0;
    protected $protoSum=array();

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

        //$this->objPHPExcel->getActiveSheet()->getStyle('A1:H8')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        //$objPHPExcel->getActiveSheet()->freezePane('A2');
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
    //
    public function setProtoValue($cityCode,$key,$value){
        $this->protoSum[$cityCode][$key]=$value;
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

    //設置表頭
    public function setDataHeardToOneArr($heardArr){
        //3.填充表格
        $i = 0;
        foreach ($heardArr as $item){
            $this->objActSheet->setCellValue($this->listArr[$i].$this->row,$item);
            $i++;
        }
    }
    //繪製表格
    public function printTable($num){
        $str = $this->listArr[$num-1];
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $this->objActSheet->getStyle('A'.($this->row-1).':'.$str.($this->row))->applyFromArray($styleArray);
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

    public function setArrProto(){
        $this->proto = array(
            "goods_code",
            "name",
            "type",
            "unit",
            "price",
            "net_weight",
            "gross_weight",
            "l_w_h",
            //"multiple",
            "origin",
            "goods_num",
            "confirm_num");

    }

    //填充內容
    public function fillDownExcel($goodList,$order_class){
        $str ="H";
        $row = $this->row;
        $this->objActSheet->setCellValue("A".$row,"物品編號");
        $this->objActSheet->setCellValue("B".$row,"物品名稱");
        $this->objActSheet->setCellValue("C".$row,"來源地");
        $this->objActSheet->setCellValue("D".$row,"物品規格");
        $this->objActSheet->setCellValue("E".$row,"要求數量");
        $this->objActSheet->setCellValue("F".$row,"物品單位");
        if($order_class == "Document"){
            $this->objActSheet->setCellValue("G".$row,'物品單價（RMB）');
            $this->objActSheet->setCellValue("H".$row,'總價（RMB）');
        }
        if($order_class == "Import"){
            $this->objActSheet->setCellValue("G".$row,'由');
            $this->objActSheet->setCellValue("H".$row,'至');
            $this->objActSheet->setCellValue("I".$row,"箱數");
            $this->objActSheet->setCellValue("J".$row,'物品單價（US$）');
            $this->objActSheet->setCellValue("K".$row,'總價（US$）');
            $this->objActSheet->setCellValue("L".$row,"净重（kg）");
            $this->objActSheet->setCellValue("M".$row,"毛重（kg）");
            $this->objActSheet->setCellValue("N".$row,"長×寬×高（cm）");
            $this->objActSheet->setCellValue("O".$row,"總淨重（kg）");
            $this->objActSheet->setCellValue("P".$row,"總毛重（kg）");
            $this->objActSheet->setCellValue("Q".$row,"體積（m³）");
            $this->objActSheet->setCellValue("R".$row,"總體積（m³）");
            $str = "R";
        }
        $row++;
        $list=array("priceSum"=>0,"voleSum"=>0,"maoSum"=>0,"jingSum"=>0);
        foreach ($goodList as $goods){
            $this->objActSheet->setCellValue("A".$row,$goods["goods_code"]);
            $this->objActSheet->setCellValue("B".$row,$goods["name"]);
            $this->objActSheet->setCellValue("C".$row,$goods["origin"]);
            $this->objActSheet->setCellValue("D".$row,$goods["type"]);
            $this->objActSheet->setCellValue("E".$row,$goods["goods_num"]);
            $this->objActSheet->setCellValue("F".$row,$goods["unit"]);
            $priceSum = intval($goods["confirm_num"])*floatval($goods["price"]);
            $list["priceSum"]+=$priceSum;
            if($order_class == "Document"){
                $this->objActSheet->setCellValue("G".$row,$goods["price"]);
                $this->objActSheet->setCellValue("H".$row,sprintf("%.2f",$priceSum));
            }
            if($order_class == "Import"){
                $vole = floatval($goods["len"])*floatval($goods["width"])*floatval($goods["height"])/1000000;
                $jingSum = intval($goods["confirm_num"])*floatval($goods["net_weight"])/intval($goods["multiple"]);
                $maoSum = intval($goods["confirm_num"])*floatval($goods["gross_weight"])/intval($goods["multiple"]);
                $voleSum = intval($goods["confirm_num"])*$vole/intval($goods["multiple"]);
                $this->objActSheet->setCellValue("G".$row,"");
                $this->objActSheet->setCellValue("H".$row,"");
                $this->objActSheet->setCellValue("I".$row,"");
                $this->objActSheet->setCellValue("J".$row,$goods["price"]);
                $this->objActSheet->setCellValue("K".$row,sprintf("%.2f",$priceSum));
                $this->objActSheet->setCellValue("L".$row,$goods["net_weight"]);
                $this->objActSheet->setCellValue("M".$row,$goods["gross_weight"]);
                $this->objActSheet->setCellValue("N".$row,$goods["len"]."×".$goods["width"]."×".$goods["height"]);
                $this->objActSheet->setCellValue("O".$row,sprintf("%.2f",$jingSum));
                $this->objActSheet->setCellValue("P".$row,sprintf("%.2f",$maoSum));
                $this->objActSheet->setCellValue("Q".$row,sprintf("%.2f",$vole));
                $this->objActSheet->setCellValue("R".$row,sprintf("%.2f",$voleSum));
                $list["jingSum"]+=$jingSum;
                $list["maoSum"]+=$maoSum;
                $list["voleSum"]+=$voleSum;
            }
            $row++;
        }
        //$this->objActSheet->getStyle('A'.$this->row.':'.$str.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $this->objActSheet->getStyle('A'.$this->row.':'.$str.($row-1))->applyFromArray($styleArray);

        if($order_class == "Document") {
            $this->objActSheet->freezePane('H'.($this->row+1));
            $this->objActSheet->setCellValue("H" . $row, sprintf("%.2f", $list["priceSum"]));
        }
        if($order_class == "Import"){
            $this->objActSheet->freezePane('G'.($this->row+1));
            $this->objActSheet->setCellValue("K".$row,sprintf("%.2f",$list["priceSum"]));
            $this->objActSheet->setCellValue("O".$row,$list["jingSum"]);
            $this->objActSheet->setCellValue("P".$row,$list["maoSum"]);
            $this->objActSheet->setCellValue("R".$row,sprintf("%.2f",$list["voleSum"]));
        }
        $cityCode = $this->objActSheet->getTitle();
        $this->protoSum[$cityCode] = $list;
    }

    //添加新的sheet
    public function addNewSheet($sheetName=""){
        $this->sheetNum++;
        $this->objPHPExcel->createSheet();
        $this->objActSheet = $this->objPHPExcel->setActiveSheetIndex($this->sheetNum);
        if(!empty($sheetName)){
            $this->objActSheet->setTitle($sheetName);
        }
    }

    //
    public function countOrderToCity($order_class){
        $protoSum = $this->protoSum;
        $row = $this->row;
        $str ="D";
        $priceSum = 0;
        $voleSum = 0;
        $maoSum = 0;
        $this->objActSheet->setCellValue("A".$row,"城市編號");
        $this->objActSheet->setCellValue("B".$row,"城市名稱");
        $this->objActSheet->setCellValue("C".$row,"公司名稱");
        $this->objActSheet->setCellValue("D".$row,'總金額（US$）');
        if($order_class == "Import"){
            $this->objActSheet->setCellValue("E".$row,"總毛重（kg）");
            $this->objActSheet->setCellValue("F".$row,"總體積（m³）");
            $str ="F";
        }
        if($order_class == "Document"){
            $this->objActSheet->setCellValue("D".$row,'總金額（RMB）');
        }
        foreach ($protoSum as $cityCode => $list){
            $row++;
            $this->objActSheet->setCellValue("A".$row,$cityCode);
            $this->objActSheet->setCellValue("B".$row,$list["city"]);
            $this->objActSheet->setCellValue("C".$row,$list["cityName"]);
            $this->objActSheet->setCellValue("D".$row,sprintf("%.2f",$list["priceSum"]));
            $priceSum +=$list["priceSum"];
            if($order_class == "Import"){
                $maoSum +=$list["maoSum"];
                $voleSum +=$list["voleSum"];
                $this->objActSheet->setCellValue("E".$row,$list["maoSum"]);
                $this->objActSheet->setCellValue("F".$row,sprintf("%.2f",$list["voleSum"]));
            }
        }
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $this->objActSheet->getStyle('A'.$this->row.':'.$str.$row)->applyFromArray($styleArray);
        $row++;
        $this->objActSheet->setCellValue("D".$row,sprintf("%.2f",$priceSum));
        if($order_class == "Import"){
            $this->objActSheet->setCellValue("E".$row,$maoSum);
            $this->objActSheet->setCellValue("F".$row,sprintf("%.2f",$voleSum));
        }
    }

    //設置sheet的名字
    public function setSheetName($sheetName){
        $this->objPHPExcel->setActiveSheetIndexByName($sheetName);
        //$this->objPHPExcel->getActiveSheet()->setTitle( 'Invoice');
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