<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods_id').$this->drawOrderArrow('goods_id'),'#',$this->createOrderLink('order-list','goods_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_num').$this->drawOrderArrow('order_num'),'#',$this->createOrderLink('order-list','order_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_user').$this->drawOrderArrow('order_user'),'#',$this->createOrderLink('order-list','order_user'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('technician').$this->drawOrderArrow('technician'),'#',$this->createOrderLink('order-list','technician'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('order-list','status'))
        ;
        ?>
    </th>
</tr>
