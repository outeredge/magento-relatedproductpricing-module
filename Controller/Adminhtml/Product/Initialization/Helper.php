<?php

namespace OuterEdge\RelatedProductPricing\Controller\Adminhtml\Product\Initialization;

use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory as ProductLinkFactory;
use Magento\Catalog\Api\ProductRepositoryInterface\Proxy as ProductRepository;
use Magento\Catalog\Model\Product\Link\Resolver as LinkResolver;
use Magento\Framework\App\ObjectManager;

class Helper extends \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper
{
    /**
     * @var LinkResolver
     */
    protected $linkResolver;

    /**
     * Setting product links
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function setProductLinks(\Magento\Catalog\Model\Product $product)
    {
        $links = $this->getLinkResolver()->getLinks();

        $product->setProductLinks([]);

        $product = $this->productLinks->initializeLinks($product, $links);
        $productLinks = $product->getProductLinks();
        $linkTypes = [
            'related' => $product->getRelatedReadonly(),
            'upsell' => $product->getUpsellReadonly(),
            'crosssell' => $product->getCrosssellReadonly()
        ];

        foreach ($linkTypes as $linkType => $readonly) {
            if (isset($links[$linkType]) && !$readonly) {
                foreach ((array) $links[$linkType] as $linkData) {
                    if (empty($linkData['id'])) {
                        continue;
                    }

                    $linkProduct = $this->getProductRepository()->getById($linkData['id']);
                    $link = $this->getProductLinkFactory()->create();
                    $link->setSku($product->getSku())
                        ->setLinkedProductSku($linkProduct->getSku())
                        ->setLinkType($linkType)
                        ->setPrice($linkData['price'])
                        ->setPosition(isset($linkData['position']) ? (int)$linkData['position'] : 0);
                    $productLinks[] = $link;
                }
            }
        }

        return $product->setProductLinks($productLinks);
    }

    /**
     * @deprecated
     * @return LinkResolver
     */
    protected function getLinkResolver()
    {
        if (!is_object($this->linkResolver)) {
            $this->linkResolver = ObjectManager::getInstance()->get(LinkResolver::class);
        }
        return $this->linkResolver;
    }

    /**
     * @return ProductRepository
     */
    protected function getProductRepository()
    {
        if (null === $this->productRepository) {
            $this->productRepository = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Api\ProductRepositoryInterface\Proxy');
        }
        return $this->productRepository;
    }

    /**
     * @return ProductLinkFactory
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
