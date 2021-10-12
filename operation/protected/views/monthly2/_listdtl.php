<tr class='clickable-row' data-href='<?php echo $this->getLink('YE01', 'monthly2/edit', 'monthly2/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YE01', 'monthly2/edit', 'monthly2/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity() || Yii::app()->user->validFunction('YN06')) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['year_no']; ?></td>
	<td><?php echo $this->record['month_no']; ?></td>
	<td><?php echo $this->record['wfstatusdesc']; ?></td>
</tr>
