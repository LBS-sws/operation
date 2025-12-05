
<?php
//2024年9月28日09:28:46
?>
<tr>
    <!--
    <th></th>
    -->
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('salesOutAudit-list','a.lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcu').$this->drawOrderArrow('a.lcu'),'#',$this->createOrderLink('salesOutAudit-list','a.lcu'))
        ;
        ?>
    </th>
    <th>
        <?php
        echo "<a href='#'>".$this->getLabelName('jd_order_type')."</a>";
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('a.order_code'),'#',$this->createOrderLink('salesOutAudit-list','a.order_code'))
        ;
        ?>
    </th>
    <th>
        <?php
            echo "<a href='#'>".Yii::t("procurement","Goods List")."</a>";
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('total_price'),'#')
        ;
        ?>
    </th>
    <?php if (!Yii::app()->user->isSingleCity()): ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('salesOutAudit-list','a.city'))
        ;
        ?>
    </th>
    <?php endif ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('salesOutAudit-list','a.status'))
        ;
        ?>
    </th>
</tr>
