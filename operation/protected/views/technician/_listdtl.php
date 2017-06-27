<tr class='clickable-row' data-href='<?php echo $this->getLink('YA01', 'technician/edit', 'technician/view', array('index'=>$this->record['id']));?>'>
    <td><?php echo $this->drawEditButton('YA01', 'technician/edit', 'technician/view', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['order_code']; ?></td>
    <td><?php echo $this->record['order_user']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['technician']; ?></td>
    <td><?php echo $this->record['lud']; ?></td>
    <?php
    switch ($this->record['status']){
        case "pending":
            echo "<td class='text-warning'>".Yii::t("procurement",$this->record['status'])."</td>";
            break;
        case "sent":
            echo "<td class='text-primary'>".Yii::t("procurement",$this->record['status'])."</td>";
            break;
        case "approve":
            echo "<td class='text-success'>".Yii::t("procurement",$this->record['status'])."</td>";
            break;
        case "reject":
            echo "<td class='text-danger'>".Yii::t("procurement",$this->record['status'])."</td>";
            break;
        case "cancelled":
            echo "<td class='text-muted'>".Yii::t("procurement",$this->record['status'])."</td>";
            break;
        default:
            echo "<td>&nbsp;</td>";
    }
    ?>
</tr>