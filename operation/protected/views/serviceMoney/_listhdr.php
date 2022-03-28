<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_code').$this->drawOrderArrow('a.service_code'),'#',$this->createOrderLink('serviceMoney-list','a.service_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('serviceMoney-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('serviceMoney-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_year').$this->drawOrderArrow('a.service_year'),'#',$this->createOrderLink('serviceMoney-list','a.service_year'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_month').$this->drawOrderArrow('a.service_month'),'#',$this->createOrderLink('serviceMoney-list','a.service_month'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_money').$this->drawOrderArrow('a.service_money'),'#',$this->createOrderLink('serviceMoney-list','a.service_money'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('score_num').$this->drawOrderArrow('a.score_num'),'#',$this->createOrderLink('serviceMoney-list','a.score_num'))
			;
		?>
	</th>
</tr>
