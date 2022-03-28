<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_code').$this->drawOrderArrow('a.service_code'),'#',$this->createOrderLink('serviceNew-list','a.service_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('serviceNew-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('serviceNew-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_year').$this->drawOrderArrow('a.service_year'),'#',$this->createOrderLink('serviceNew-list','a.service_year'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_month').$this->drawOrderArrow('a.service_month'),'#',$this->createOrderLink('serviceNew-list','a.service_month'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_num').$this->drawOrderArrow('a.service_num'),'#',$this->createOrderLink('serviceNew-list','a.service_num'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('score_num').$this->drawOrderArrow('a.score_num'),'#',$this->createOrderLink('serviceNew-list','a.score_num'))
			;
		?>
	</th>
</tr>
