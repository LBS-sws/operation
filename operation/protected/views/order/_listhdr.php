<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('order_code'),'#',$this->createOrderLink('order-list','order_code'))
        ;
        ?>
    </th>
    <th>
        <?php
            echo "<a href='#'>".Yii::t("procurement","Goods List")."</a>";
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
