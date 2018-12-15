<?php
namespace Silk\Webapi\Model;


use Magento\Setup\Exception;

abstract class AbstractApi
{
    /**
     * @var \Silk\Webapi\Api\Data\ResultInterfaceFactory
     */
    protected $resultFactory;

    protected $timezone;

    public function __construct(
        Context $context
    )
    {
        $this->resultFactory = $context->getResultFactory();
        $this->timezone = $context->getTimezone();
    }

    /**
     * Retrieve formatting date
     *
     * @param null|string|\DateTime $date
     * @param int $format
     * @param bool $showTime
     * @param null|string $timezone
     * @return string
     */
    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->timezone->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }

    /**
     * Retrieve formatting time
     *
     * @param   \DateTime|string|null $time
     * @param   int $format
     * @param   bool $showDate
     * @return  string
     */
    public function formatTime(
        $time = null,
        $format = \IntlDateFormatter::SHORT,
        $showDate = false
    ) {
        $time = $time instanceof \DateTimeInterface ? $time : new \DateTime($time);
        return $this->timezone->formatDateTime(
            $time,
            $showDate ? $format : \IntlDateFormatter::NONE,
            $format
        );
    }

    /**
     * @param string $string
     * @param string $formate
     * @return false|string
     */
    public function formatDateTime($string, $formate = 'Y-m-d H:i:s')
    {
        return date($formate, strtotime($string));
    }

    /**
     * @param $message
     * @param int $code
     * @throws \Exception
     */
    public function throwException($message, $code = 0)
    {
        throw new \Magento\Framework\Webapi\Exception(__($message), $code);
    }
}