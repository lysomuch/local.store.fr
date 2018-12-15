<?php
/**
 * Copyright © Yogesh Khasturi. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Silk\Geoip\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

}
