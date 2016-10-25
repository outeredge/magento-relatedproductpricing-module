<?php

namespace OuterEdge\RelatedProductPricing\Model\ProductLink;

class Link extends \Magento\Catalog\Model\ProductLink\Link
{
    const KEY_PRICE = 'price';

    /**
     * Get product link price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->_get(self::KEY_PRICE);
    }

    /**
     * Set linked item price
     *
     * @param int $price
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData(self::KEY_PRICE, $price);
    }
}
