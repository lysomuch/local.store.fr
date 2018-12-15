<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab\Content;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class EmptyElement extends \Magento\Backend\Block\Template implements RendererInterface
{
    protected $_template = 'googlewizard/empty.phtml';

    /**
     * Render element
     *
     * Render empty element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $this->setElement($element);
        return $this->toHtml();
    }
}
