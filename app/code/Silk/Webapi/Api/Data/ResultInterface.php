<?php
namespace Silk\Webapi\Api\Data;

interface ResultInterface
{
    const CODE = 'code';
    const MESSAGE = 'message';
    const RESULT = 'result';

    /**
     * @param integer $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @param mixed $result
     * @param null|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return $this
     */
    public function setResult($result, \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection = null);

    /**
     * @return integer
     */
    public function getCode();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return mixed
     */
    public function getResult();
}