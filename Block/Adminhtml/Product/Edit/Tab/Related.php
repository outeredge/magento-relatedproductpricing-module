<?php

namespace OuterEdge\RelatedProductPricing\Block\Adminhtml\Product\Edit\Tab;

class Related extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Related
{
    /**
     * Add columns to grid
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'number',
                'index' => 'price',
                'editable' => true,
                'edit_only' => $this->getProduct()->getId(),
            ]
        );

    }
}
