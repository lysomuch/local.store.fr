<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/9/13
 * Time: 16:10
 */

namespace Silk\Cms\Plugin\Model;

class AbstractTemplate extends \Magento\Email\Model\AbstractTemplate
{
    public function aroundSetForcedArea($subject, $proceed, $templateId) {
        if ( ! isset($subject->area)) {
            $subject->area = $this->emailConfig->getTemplateArea($templateId);
        }
        return $subject;
    }

    /**
     * Getter for filter factory that is specific to the type of template being processed
     *
     * @return mixed
     */
    protected function getFilterFactory()
    {
        // TODO: Implement getFilterFactory() method.
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    public function getType()
    {
        // TODO: Implement getType() method.
    }
}