
<?php
//2024年9月28日09:28:46
?>
<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods_code').$this->drawOrderArrow('goods_code'),'#',$this->createOrderLink('warehouse-list','goods_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('warehouse-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('jd_classify_name').$this->drawOrderArrow('jd_classify_name'),'#',$this->createOrderLink('warehouse-list','jd_classify_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('unit').$this->drawOrderArrow('unit'),'#',$this->createOrderLink('warehouse-list','unit'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('display').$this->drawOrderArrow('display'),'#',$this->createOrderLink('warehouse-list','display'))
        ;
        ?>
    </th>
    <?php if (Yii::app()->user->validFunction('YN02')): ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('price'),'#')
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('cost_price'),'#')
        ;
        ?>
    </th>
    <?php endif ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('inventory').$this->drawOrderArrow('inventory'),'#')
        ;
        ?>
    </th>
</tr>
