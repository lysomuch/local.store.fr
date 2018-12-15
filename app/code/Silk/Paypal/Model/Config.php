<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-7-10 下午2:08
 */


namespace Silk\Paypal\Model;


class Config extends \Magento\Paypal\Model\Config
{
    /**
     * Return start url for PayPal Basic
     *
     * @param string $token
     * @return string
     */
    public function getPayPalBasicStartUrl($token)
    {
        $params = [
            'cmd'   => '_express-checkout',
            'token' => $token,
            'locale.x' => 'en_GB',
            'landingpage' => 'billing'
        ];

        if ($this->isOrderReviewStepDisabled()) {
            $params['useraction'] = 'commit';
        }

        return $this->getPaypalUrl($params);
    }
}