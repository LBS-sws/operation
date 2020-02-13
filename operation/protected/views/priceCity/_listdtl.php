<tr class='clickable-row' data-href='<?php echo $this->getLink('YG07', 'priceCity/edit', 'priceCity/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YG07', 'priceCity/edit', 'priceCity/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['price_type']; ?></td>
</tr>
