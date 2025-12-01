
<?php
//2024年9月28日09:28:46
?>
<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('salesOut-list','a.lcd'))
        ;
        ?>
    </th>
    <th>
        <?php
        echo "<a href='#'>".$this->getLabelName('jd_order_type')."</a>";
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('a.order_code'),'#',$this->createOrderLink('salesOut-list','a.order_code'))
        ;
        ?>
    </th>
    <th>
        <?php
            echo "<a href='#'>".Yii::t("procurement","Goods List")."</a>";
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('salesOut-list','a.status'))
        ;
        ?>
    </th>
</tr>
