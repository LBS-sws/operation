<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('priceCity-list','a.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('price_type').$this->drawOrderArrow('b.price_type'),'#',$this->createOrderLink('priceCity-list','b.price_type'))
        ;
        ?>
    </th>
</tr>
