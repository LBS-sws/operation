<tr class='clickable-row' data-href='<?php echo $this->getLink('TL05', 'serviceMoney/edit', 'serviceMoney/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('TL05', 'serviceMoney/edit', 'serviceMoney/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['service_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['service_year']; ?></td>
	<td><?php echo $this->record['service_month']; ?></td>
	<td><?php echo $this->record['service_money']; ?></td>
	<td><?php echo $this->record['score_num']; ?></td>
	<td><?php echo $this->record['night_money']; ?></td>
	<td><?php echo $this->record['night_score']; ?></td>
	<td><?php echo $this->record['create_money']; ?></td>
	<td><?php echo $this->record['create_score']; ?></td>
</tr>
