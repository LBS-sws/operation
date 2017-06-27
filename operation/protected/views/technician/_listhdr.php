<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('order_code'),'#',$this->createOrderLink('technician-list','order_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_user').$this->drawOrderArrow('order_user'),'#',$this->createOrderLink('technician-list','order_user'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('lcd'),'#',$this->createOrderLink('technician-list','lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('technician').$this->drawOrderArrow('technician'),'#',$this->createOrderLink('technician-list','technician'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lud').$this->drawOrderArrow('lud'),'#',$this->createOrderLink('technician-list','lud'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('technician-list','status'))
        ;
        ?>
    </th>
</tr>
