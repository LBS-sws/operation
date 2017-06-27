<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('activity_code').$this->drawOrderArrow('activity_code'),'#',$this->createOrderLink('activity-list','activity_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('activity_title').$this->drawOrderArrow('activity_title'),'#',$this->createOrderLink('activity-list','activity_title'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('activity-list','start_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('activity-list','end_time'))
        ;
        ?>
    </th>
</tr>
