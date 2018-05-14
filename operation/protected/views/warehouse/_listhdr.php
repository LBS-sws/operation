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
        <?php echo TbHtml::link($this->getLabelName('classify_id').$this->drawOrderArrow('classify_id'),'#',$this->createOrderLink('warehouse-list','classify_id'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('unit').$this->drawOrderArrow('unit'),'#',$this->createOrderLink('warehouse-list','unit'))
        ;
        ?>
    </th>
    <?php if (Yii::app()->user->validFunction('YN02')): ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('price').$this->drawOrderArrow('price'),'#',$this->createOrderLink('warehouse-list','price'))
        ;
        ?>
    </th>
    <?php endif ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('inventory').$this->drawOrderArrow('inventory'),'#',$this->createOrderLink('warehouse-list','inventory'))
        ;
        ?>
    </th>
</tr>
