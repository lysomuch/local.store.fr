<?php
namespace Silk\Webapi\Model;


class Context implements \Magento\Framework\ObjectManager\ContextInterface
{
    protected $resultFactory;

    protected $timezone;

    public function __construct
    (
        \Silk\Webapi\Api\Data\ResultInterfaceFactory $resultFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    )
    {
        $this->resultFactory = $resultFactory;
        $this->timezone = $timezone;
    }

    /**
     * @return \Silk\Webapi\Api\Data\ResultInterfaceFactory
     */
    public function getResultFactory()
    {
        return $this->resultFactory;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}