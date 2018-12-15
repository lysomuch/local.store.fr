<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml review main block
 */
namespace Silk\Gift\Block\Adminhtml;

class Product extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize add new review
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_addButtonLabel = __('New Gift Product');
        parent::_construct();

        $this->_blockGroup = 'Silk_Gift';
        $this->_controller = 'adminhtml_product';

        $this->_headerText = __('All Gift Product');

    }
}
