<tr class='clickable-row' data-href='<?php echo $this->getLink('YG01', 'goodsim/edit', 'goodsim/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YG01', 'goodsim/edit', 'goodsim/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['goods_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['type']; ?></td>
	<td><?php echo $this->record['unit']; ?></td>
	<td><?php echo sprintf("%.2f", $this->record['price']); ?></td>
	<td><?php echo sprintf("%.2f", $this->record['price_two']); ?></td>
    <td><?php echo $this->record['net_weight']; ?></td>
    <td><?php echo $this->record['gross_weight']; ?></td>
    <td>
        <?php
        if(!empty($this->record['img_url'])){
            echo "<span class='fa fa-file-image-o'></span>";
        }
        ?>
    </td>
</tr>
