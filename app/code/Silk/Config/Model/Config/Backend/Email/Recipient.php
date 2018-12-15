<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/15
 * Time: 15:52
 */

namespace Silk\Config\Model\Config\Backend\Email;

/**
 * @api
 * @since 100.0.2
 */
class Recipient extends \Magento\Framework\App\Config\Value
{
    /**
     * Check recipient name validity
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (!preg_match("/^[\S ]+$/", $value)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The recipient name "%1" is not valid. Please use only visible characters and spaces.', $value)
            );
        }

        if (strlen($value) > 255) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Maximum recipient name length is 255. Please correct your settings.'));
        }
        return $this;
    }
}