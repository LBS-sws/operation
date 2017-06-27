<?php
    if($this->record['activity_status'] == "Run"){
        echo "<tr class='text-success'>";
    }else{
        echo "<tr class='text-warning'>";
    }
?>
	<td><?php echo $this->drawEditButton('YS01', 'purchase/detail', 'purchase/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['activity_code']; ?></td>
	<td><?php echo $this->record['activity_title']; ?></td>
	<td><?php echo $this->record['start_time']; ?></td>
	<td><?php echo $this->record['end_time']; ?></td>
	<td><?php echo $this->record['order_sum']; ?></td>
<!-- 訂單類型數量
	<td>
        <?php
        foreach ($this->record['order_type_sum'] as $key =>$sumList){
            echo Yii::t("procurement",$key).Yii::t("procurement","Order")." × ".$sumList."<br>";
        }
         ?>
    </td>
    -->
	<td><?php echo Yii::t("procurement",$this->record['activity_status']); ?></td>
    <td><?php echo $this->drawEditButton('YS11', 'purchase/edit', 'purchase/view', array('index'=>$this->record['id'])); ?></td>
</tr>
