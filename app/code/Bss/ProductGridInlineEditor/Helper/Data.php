<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductGridInlineEditor
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductGridInlineEditor\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED              = 'product_grid_inline_editor/general/active';
    const XML_PATH_TYPE_ALLOW           = 'product_grid_inline_editor/general/type_allow';
    const XML_PATH_MASS_EDIT            = 'product_grid_inline_editor/general/mass_edit';
    const XML_PATH_SINGLE_EDIT_FIELD    = 'product_grid_inline_editor/general/single_edit_filed';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getInputTypeAllow()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TYPE_ALLOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isMassEdit()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_MASS_EDIT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isSingleEditField()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SINGLE_EDIT_FIELD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
