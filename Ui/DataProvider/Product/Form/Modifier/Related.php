<?php
namespace OuterEdge\RelatedProductPricing\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;

class Related extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Related
{
    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getGrid($scope)
    {
        $array = parent::getGrid($scope);

        return array_merge(
            $array, [
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => ($scope == static::DATA_SCOPE_RELATED ? $this->fillMetaRelated(): $this->fillMeta()),
                ],
            ],
        ]);
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMetaRelated()
    {
        $array = parent::fillMeta();

        return array_merge(
            $array, [
            'price' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('PriceEditable'),
                            'dataType' => Number::NAME,
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataScope' => 'price',
                           // 'addbefore' => $this->getCurrencySymbol(),
                            'sortOrder' => 60,
                            'visible' => true,
                            'fit' => true,
                            'validation' => [
                                'validate-zero-or-greater' => true
                            ],
                        ],
                    ],
                ],
            ]
        ]);
    }

    /**
     * Prepare data column
     *
     * @param ProductInterface $linkedProduct
     * @param ProductLinkInterface $linkItem
     * @return array
     */
    protected function fillData(ProductInterface $linkedProduct, ProductLinkInterface $linkItem)
    {
        return [
            'id' => $linkedProduct->getId(),
            'thumbnail' => $this->imageHelper->init($linkedProduct, 'product_listing_thumbnail')->getUrl(),
            'name' => $linkedProduct->getName(),
            'status' => $this->status->getOptionText($linkedProduct->getStatus()),
            'attribute_set' => $this->attributeSetRepository
                ->get($linkedProduct->getAttributeSetId())
                ->getAttributeSetName(),
            'sku' => $linkItem->getLinkedProductSku(),
            'price' => ($linkItem->getPrice() !== null ?  $linkItem->getPrice() : $linkedProduct->getPrice()),
            'position' => $linkItem->getPosition(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        foreach ($this->getDataScopes() as $dataScope) {
            $data[$productId]['links'][$dataScope] = [];
            foreach ($this->productLinkRepository->getList($product) as $linkItem) {
                if ($linkItem->getLinkType() !== $dataScope) {
                    continue;
                }

                /** @var \Magento\Catalog\Model\Product $linkedProduct */
                $linkedProduct = $this->productRepository->get(
                    $linkItem->getLinkedProductSku(),
                    false,
                    $this->locator->getStore()->getId()
                );
                $data[$productId]['links'][$dataScope][] = $this->fillData($linkedProduct, $linkItem);
            }
            if (!empty($data[$productId]['links'][$dataScope])) {
                $dataMap = [
                    'data' => [
                        'items' => $data[$productId]['links'][$dataScope]
                    ]
                ];
                $data[$productId]['links'][$dataScope] = $dataMap['data']['items'];
            }
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->locator->getStore()->getId();

        return $data;
    }
}
