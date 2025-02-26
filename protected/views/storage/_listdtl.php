<tr class='clickable-row <?php echo $this->record['color'];?>' data-href='<?php echo $this->getLink('YD08', 'storage/edit', 'storage/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YD08', 'storage/edit', 'storage/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['code']; ?></td>
	<td><?php echo $this->record['apply_time']; ?></td>
	<td><?php echo $this->record['storage_name']; ?></td>
	<td><?php echo $this->record['status_type']; ?></td>
</tr>
