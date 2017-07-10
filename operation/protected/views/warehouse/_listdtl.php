<tr class='clickable-row' data-href='<?php echo $this->getLink('YD01', 'warehouse/edit', 'warehouse/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YD01', 'warehouse/edit', 'warehouse/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['goods_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['type']; ?></td>
	<td><?php echo $this->record['unit']; ?></td>
	<td><?php echo $this->record['inventory']; ?></td>
</tr>
