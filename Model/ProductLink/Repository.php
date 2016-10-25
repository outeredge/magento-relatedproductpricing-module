<?php

namespace OuterEdge\RelatedProductPricing\Model\ProductLink;

use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Model\Product\LinkTypeProvider;

class Repository extends \Magento\Catalog\Model\ProductLink\Repository
{
    /**
     * Get product links list
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Catalog\Api\Data\ProductLinkInterface[]
     */
    public function getList(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $output = [];
        $linkTypes = $this->getLinkTypeProvider()->getLinkTypes();
        foreach (array_keys($linkTypes) as $linkTypeName) {

            $collection = $this->entityCollectionProvider->getCollection($product, $linkTypeName);
            foreach ($collection as $item) {
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
                $productLink = $this->getProductLinkFactory()->create();
                $productLink->setSku($product->getSku())
                    ->setLinkType($linkTypeName)
                    ->setLinkedProductSku($item['sku'])
                    ->setLinkedProductType($item['type'])
                    ->setPrice($item['price'])
                    ->setPosition($item['position']);
                if (isset($item['custom_attributes'])) {
                    $productLinkExtension = $productLink->getExtensionAttributes();
                    if ($productLinkExtension === null) {
                        $productLinkExtension = $this->getProductLinkExtensionFactory()->create();
                    }
                    foreach ($item['custom_attributes'] as $option) {
                        $name = $option['attribute_code'];
                        $value = $option['value'];
                        $setterName = 'set'.ucfirst($name);
                        // Check if setter exists
                        if (method_exists($productLinkExtension, $setterName)) {
                            call_user_func([$productLinkExtension, $setterName], $value);
                        }
                    }
                    $productLink->setExtensionAttributes($productLinkExtension);
                }
                $output[] = $productLink;
            }
        }
        return $output;
    }

    /**
     * @return LinkTypeProvider
     */
    protected function getLinkTypeProvider()
    {
        if (null === $this->linkTypeProvider) {
            $this->linkTypeProvider = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Model\Product\LinkTypeProvider');
        }
        return $this->linkTypeProvider;
    }

    /**
     * @return ProductLinkInterfaceFactory
     */
    protected function getProductLinkFactory()
    {
        if (null === $this->productLinkFactory) {
            $this->productLinkFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Api\Data\ProductLinkInterfaceFactory');
        }
        return $this->productLinkFactory;
    }

}
