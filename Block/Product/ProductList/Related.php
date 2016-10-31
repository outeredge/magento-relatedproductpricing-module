<?php

namespace OuterEdge\RelatedProductPricing\Block\Product\ProductList;

/**
 * Catalog product related items block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Related extends \Magento\Catalog\Block\Product\ProductList\Related
{
    /**
     * @return $this
     */
    protected function _prepareData()
    {
        $product = $this->_coreRegistry->registry('product');
        /* @var $product \Magento\Catalog\Model\Product */

        $this->_itemCollection = $product->getRelatedProductCollection()->addAttributeToSelect(
            'required_options'
        )->setPositionOrder()->addStoreFilter();

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            //$this->_addProductAttributesAndPrices($this->_itemCollection);
        }

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
            if ($product->getLinkId() && $product->getPrice()){
                $product->setTierPrice($product->getPrice());
                $product->setMinimalPrice($product->getPrice());
                $product->setMinPrice($product->getPrice());
                $product->setFinalPrice($product->getPrice());
                $product->setMaxPrice($product->getPrice());
            }
        }

        return $this;
    }

}
