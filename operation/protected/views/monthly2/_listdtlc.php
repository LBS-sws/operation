<tr class='clickable-row' data-href='<?php echo $this->getLink('YE02', 'monthly2/view', 'monthly2/view', array('index'=>$this->record['id'],'rtn'=>'indexc'));?>'>
	<td><?php echo $this->drawEditButton('YE02', 'monthly2/view', 'monthly2/view', array('index'=>$this->record['id'],'rtn'=>'indexc')); ?></td>
<?php if (!Yii::app()->user->isSingleCity() || Yii::app()->user->validFunction('YN06')) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['year_no']; ?></td>
	<td><?php echo $this->record['month_no']; ?></td>
	<td><?php echo $this->record['val_1']; ?></td>
	<td><?php echo $this->record['val_2']; ?></td>
	<td><?php echo $this->record['val_3']; ?></td>
	<td><?php echo $this->record['val_4']; ?></td>
	<td><?php echo $this->record['wfstatusdesc']; ?></td>
</tr>
