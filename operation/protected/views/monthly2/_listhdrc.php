<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('staff-list','city_name'))
			;
		?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('year_no').$this->drawOrderArrow('year_no'),'#',$this->createOrderLink('monthly-list','year_no'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('month_no').$this->drawOrderArrow('month_no'),'#',$this->createOrderLink('monthly-list','month_no'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link(Yii::t("monthly",'空气服务收入').$this->drawOrderArrow('val_1'),'#',$this->createOrderLink('monthly-list','val_1'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link(Yii::t("monthly",'机器售卖收入').$this->drawOrderArrow('val_2'),'#',$this->createOrderLink('monthly-list','val_2'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link(Yii::t("monthly",'延长维保收入').$this->drawOrderArrow('val_3'),'#',$this->createOrderLink('monthly-list','val_3'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link(Yii::t("monthly",'收入合计').$this->drawOrderArrow('val_4'),'#',$this->createOrderLink('monthly-list','val_4'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('wfstatusdesc').$this->drawOrderArrow('wfstatusdesc'),'#',$this->createOrderLink('monthly-list','wfstatusdesc'))
			;
		?>
	</th>
</tr>