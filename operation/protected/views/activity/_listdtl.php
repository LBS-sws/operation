<tr class='clickable-row' data-href='<?php echo $this->getLink('YS03', 'activity/edit', 'activity/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YS03', 'activity/edit', 'activity/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['activity_code']; ?></td>
	<td><?php echo $this->record['activity_title']; ?></td>
	<td><?php echo $this->record['order_class']; ?></td>
	<td><?php echo $this->record['start_time']; ?></td>
	<td><?php echo $this->record['end_time']; ?></td>
	<td><?php echo $this->record['num']; ?></td>
</tr>
