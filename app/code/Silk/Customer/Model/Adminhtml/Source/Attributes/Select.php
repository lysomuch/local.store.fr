<?php
/**
 * All rights reserved.
 *
 * @authors daniel (luo3555@qq.com)
 * @date    18-5-30 下午4:35
 * @version 0.1.0
 */


namespace Silk\Customer\Model\Adminhtml\Source\Attributes;


class Select
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * Select constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->customerFactory = $customerFactory;
        $this->escaper = $escaper;
    }

    /**
     * Customer custom attributes.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        //exclude attributes from mapping

        $options = [
            [
                'value' => 1,
                'label' => 'Test'
            ]
        ];

        return $options;
    }
}