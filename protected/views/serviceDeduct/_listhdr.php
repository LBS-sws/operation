<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_code').$this->drawOrderArrow('a.service_code'),'#',$this->createOrderLink('serviceDeduct-list','a.service_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('serviceDeduct-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('serviceDeduct-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('deduct_date').$this->drawOrderArrow('a.deduct_date'),'#',$this->createOrderLink('serviceDeduct-list','a.deduct_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('deduct_type').$this->drawOrderArrow('a.deduct_type'),'#',$this->createOrderLink('serviceDeduct-list','a.deduct_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('score_num').$this->drawOrderArrow('a.score_num'),'#',$this->createOrderLink('serviceDeduct-list','a.score_num'))
			;
		?>
	</th>
</tr>
