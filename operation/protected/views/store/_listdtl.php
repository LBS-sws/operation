<!-- -->
<tr class='clickable-row' data-href='<?php echo $this->getLink('YD10', 'store/edit', 'store/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YD10', 'store/edit', 'store/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['jd_store_no']; ?></td>
	<td><?php echo $this->record['store_type']; ?></td>
	<td><?php echo $this->record['z_display']; ?></td>
</tr>
