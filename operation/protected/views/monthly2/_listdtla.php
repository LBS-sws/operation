<tr class='clickable-row' data-href='<?php echo $this->getLink('YE03', 'monthly2/view', 'monthly2/view', array('index'=>$this->record['id'],'rtn'=>'indexa'));?>'>
	<td><?php echo $this->drawEditButton('YE03', 'monthly2/view', 'monthly2/view', array('index'=>$this->record['id'],'rtn'=>'indexa')); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['year_no']; ?></td>
	<td><?php echo $this->record['month_no']; ?></td>
</tr>
