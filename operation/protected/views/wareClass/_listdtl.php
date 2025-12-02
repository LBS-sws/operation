
<?php
//2024年9月28日09:28:46
?>
<tr class='clickable-row' data-href='<?php echo $this->getLink('YD13', 'wareClass/edit', 'wareClass/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YD13', 'wareClass/edit', 'wareClass/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['goods_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['class_str']; ?></td>
	<td><?php echo $this->record['class_report']; ?></td>
</tr>
