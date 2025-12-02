
<?php
//2024年9月28日09:28:46
?>
<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods_code').$this->drawOrderArrow('a.goods_code'),'#',$this->createOrderLink('wareClass-list','a.goods_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('wareClass-list','a.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('class_str').$this->drawOrderArrow('b.class_str'),'#',$this->createOrderLink('wareClass-list','b.class_str'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('class_report').$this->drawOrderArrow('b.class_report'),'#',$this->createOrderLink('wareClass-list','b.class_report'))
        ;
        ?>
    </th>
</tr>
