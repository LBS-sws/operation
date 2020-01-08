<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('cargoCost-list','a.lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcu').$this->drawOrderArrow('a.lcu'),'#',$this->createOrderLink('cargoCost-list','a.lcu'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('a.order_code'),'#',$this->createOrderLink('cargoCost-list','a.order_code'))
        ;
        ?>
    </th>
    <th>
        <?php
            echo "<a href='#'>".Yii::t("procurement","Goods List")."</a>";
        ?>
    </th>
    <?php if (!Yii::app()->user->isSingleCity()): ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('cargoCost-list','a.city'))
        ;
        ?>
    </th>
    <?php endif ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('cargoCost-list','a.status'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('total_price').$this->drawOrderArrow('a.total_price'),'#',$this->createOrderLink('cargoCost-list','a.total_price'))
        ;
        ?>
    </th>
</tr>
