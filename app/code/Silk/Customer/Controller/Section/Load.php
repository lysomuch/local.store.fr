<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Customer\Controller\Section;

use Magento\Customer\CustomerData\Section\Identifier;
use Magento\Customer\CustomerData\SectionPoolInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Customer section controller
 */
class Load extends \Magento\Customer\Controller\Section\Load
{
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /** @var \Magento\Checkout\Model\Cart  */
    protected $cart;

    /**
     * Load constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Identifier $sectionIdentifier
     * @param SectionPoolInterface $sectionPool
     * @param \Magento\Checkout\Model\Cart $cart
     * @param $escaper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Identifier $sectionIdentifier,
        SectionPoolInterface $sectionPool,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Escaper $escaper = null
    )
    {
        parent::__construct($context, $resultJsonFactory, $sectionIdentifier, $sectionPool, $escaper);
        $this->cart = $cart;
        $this->escaper = $escaper ?: $this->_objectManager->get(\Magento\Framework\Escaper::class);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store', true);
        $resultJson->setHeader('Pragma', 'no-cache', true);
        try {
            $sectionNames = $this->getRequest()->getParam('sections');
            $sectionNames = $sectionNames ? array_unique(\explode(',', $sectionNames)) : null;
            if (is_array($sectionNames) && in_array('cart', $sectionNames)) {
                $this->cart->save();
            }
            $updateSectionId = $this->getRequest()->getParam('update_section_id');
            if ('false' === $updateSectionId) {
                $updateSectionId = false;
            }
            $response = $this->sectionPool->getSectionsData($sectionNames, (bool)$updateSectionId);
        } catch (\Exception $e) {
            $resultJson->setStatusHeader(
                \Zend\Http\Response::STATUS_CODE_400,
                \Zend\Http\AbstractMessage::VERSION_11,
                'Bad Request'
            );
            $response = ['message' => $this->escaper->escapeHtml($e->getMessage())];
        }

        return $resultJson->setData($response);
    }
}
