<?php

namespace OuterEdge\RelatedProductPricing\Model\ProductLink\Converter;

class DefaultConverter extends \Magento\Catalog\Model\ProductLink\Converter\DefaultConverter
{
    public function convert(\Magento\Catalog\Model\Product $product)
    {
        return [
            'type' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'position' => $product->getPosition(),
            'price' => $product->getPrice()
        ];
    }
}
