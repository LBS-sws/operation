<tr class='clickable-row' data-href='<?php echo $this->getLink('TL06', 'serviceNew/edit', 'serviceNew/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('TL06', 'serviceNew/edit', 'serviceNew/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['service_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['service_year']; ?></td>
	<td><?php echo $this->record['service_month']; ?></td>
	<td><?php echo $this->record['service_num']; ?></td>
	<td><?php echo $this->record['score_num']; ?></td>
</tr>
