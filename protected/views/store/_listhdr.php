
<?php
//2024年9月28日09:28:46
?>
<!-- -->
<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('store-list','b.name'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('store-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('jd_store_no').$this->drawOrderArrow('a.jd_store_no'),'#',$this->createOrderLink('store-list','a.jd_store_no'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('store_type').$this->drawOrderArrow('a.store_type'),'#',$this->createOrderLink('store-list','a.store_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_display').$this->drawOrderArrow('a.z_display'),'#',$this->createOrderLink('store-list','a.z_display'))
			;
		?>
	</th>
</tr>
