<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('c.order_code'),'#',$this->createOrderLink('warehouseBack-list','c.order_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('disp_name').$this->drawOrderArrow('d.disp_name'),'#',$this->createOrderLink('warehouseBack-list','d.disp_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods_code').$this->drawOrderArrow('b.goods_code'),'#',$this->createOrderLink('warehouseBack-list','b.goods_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('warehouseBack-list','b.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('unit').$this->drawOrderArrow('b.unit'),'#',$this->createOrderLink('warehouseBack-list','b.unit'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('back_num').$this->drawOrderArrow('a.back_num'),'#',$this->createOrderLink('warehouseBack-list','a.back_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('back_user').$this->drawOrderArrow('a.lcu'),'#',$this->createOrderLink('warehouseBack-list','a.lcu'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('warehouseBack-list','a.lcd'))
        ;
        ?>
    </th>
</tr>
