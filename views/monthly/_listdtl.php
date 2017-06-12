<tr class='clickable-row' data-href='<?php echo $this->getLink('YA01', 'monthly/edit', 'monthly/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('view', 'monthly/edit', 'monthly/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['year_no']; ?></td>
	<td><?php echo $this->record['month_no']; ?></td>
</tr>
