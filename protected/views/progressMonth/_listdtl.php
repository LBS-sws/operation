<tr class="<?php echo $this->record['color']; ?>">
	<td><?php echo $this->recordptr+1; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['progress_date']; ?></td>
	<td><?php echo $this->record['score_sum']; ?></td>
	<td><?php echo $this->record['last_sum']; ?></td>
    <td><?php echo $this->record['progress_rate'].(empty($this->record['progress_rate'])?"":"%"); ?></td>
</tr>
