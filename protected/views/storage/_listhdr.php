<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('code'),'#',$this->createOrderLink('storage-list','code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_time').$this->drawOrderArrow('apply_time'),'#',$this->createOrderLink('storage-list','apply_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('storage_name').$this->drawOrderArrow('storage_name'),'#',$this->createOrderLink('storage-list','storage_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status_type').$this->drawOrderArrow('status_type'),'#',$this->createOrderLink('storage-list','status_type'))
        ;
        ?>
    </th>
</tr>
