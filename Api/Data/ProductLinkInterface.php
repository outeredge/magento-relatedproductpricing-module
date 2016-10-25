<?php

namespace OuterEdge\RelatedProductPricing\Api\Data;

/**
 * @api
 */
interface ProductLinkInterface extends \Magento\Catalog\Api\Data\ProductLinkInterface
{
    /**
     * Get linked item price
     *
     * @return int
     */
    public function getPrice();

     /**
     * Set price
     *
     * @param string $price
     * @return $this
     */
    public function setPrice($price);

}
