<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('classify-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('level').$this->drawOrderArrow('level'),'#',$this->createOrderLink('classify-list','level'))
        ;
        ?>
    </th>
</tr>
