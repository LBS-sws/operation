<tr class='clickable-row' data-href='<?php echo $this->getLink('TL07', 'serviceDeduct/edit', 'serviceDeduct/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('TL07', 'serviceDeduct/edit', 'serviceDeduct/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['service_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['deduct_date']; ?></td>
	<td><?php echo $this->record['deduct_type']; ?></td>
	<td><?php echo $this->record['score_num']; ?></td>
</tr>
