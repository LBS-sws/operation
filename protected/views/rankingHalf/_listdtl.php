

<?php if ($this->record['show']): ?>
<tr class='clickable-row' data-href='<?php echo $this->getLink('TL03', 'rankingHalf/edit', 'rankingHalf/view', array('index'=>$this->record['id'],'rank'=>$this->record['rank'],'year'=>$this->record['year'],'month'=>$this->record['month']));?>'>
    <td><?php echo $this->drawEditButton('TL03', 'rankingHalf/edit', 'rankingHalf/view', array('index'=>$this->record['id'],'rank'=>$this->record['rank'],'year'=>$this->record['year'],'month'=>$this->record['month'])); ?></td>
    <?php else: ?>
<tr>
    <td>&nbsp;</td>
    <?php endif ?>

	<td><?php echo $this->record['rank']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['rank_year']; ?></td>
	<td><?php echo $this->record['rank_month']; ?></td>
	<td><?php echo $this->record['score_sum']; ?></td>
</tr>
