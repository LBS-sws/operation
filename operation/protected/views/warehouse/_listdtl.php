
<?php
//2024年9月28日09:28:46
?>
<tr class='clickable-row<?php echo $this->record['color']; ?>' data-href='<?php echo $this->getLink('YD01', 'warehouse/edit', 'warehouse/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YD01', 'warehouse/edit', 'warehouse/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['goods_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['jd_classify_name']; ?></td>
	<td><?php echo $this->record['unit']; ?></td>
	<td><?php echo $this->record['display']; ?></td>
    <?php if (Yii::app()->user->validFunction('YN02')): ?>
	<td>
        <?php echo $this->record['price']; ?>
    </td>
	<td>
        <?php echo TbHtml::button(Yii::t('procurement','price history'), array(
            'data-id'=>$this->record['id'],'class'=>'clickPriceBtn'));
        ?>
    </td>
    <?php endif ?>
	<td>
        <?php
        $good_code=$this->record['goods_code'];
        $inventory = key_exists($good_code,$this->model->inventoryJD)?$this->model->inventoryJD[$good_code]["jd_store_sum"]:"0";
        echo $inventory;
        ?>
        <span class="fa fa-question-circle goodsHistoryMouse" data-id="<?php echo $this->record['id'];?>"></span>
    </td>
</tr>
