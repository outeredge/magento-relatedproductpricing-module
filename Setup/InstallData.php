<?php

namespace OuterEdge\RelatedProductPricing\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $select = $setup->getConnection()
            ->select()
            ->from(
                ['c' => $setup->getTable('catalog_product_link_attribute')]
            )
            ->where(
                "c.product_link_attribute_code='price' AND c.link_type_id=?",
                \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED
            );
        $result = $setup->getConnection()->fetchAll($select);

        if (!$result) {
            $data = [
                [
                    'link_type_id' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED,
                    'product_link_attribute_code' => 'price',
                    'data_type' => 'decimal',
                ]
            ];

            $setup->getConnection()->insertMultiple($setup->getTable('catalog_product_link_attribute'), $data);
        }
    }

}