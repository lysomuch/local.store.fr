<?php
namespace Silk\Webapi\Model\Api\Data;

use Magento\Framework\DataObject;
use Silk\Webapi\Api\Data\ResultInterface;

class Result extends DataObject implements ResultInterface
{

    /**
     * @param integer $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @param mixed $result
     * @param null|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return $this
     */
    public function setResult($result, \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection = null)
    {
        $results = [];
        if ($collection && is_array($result)) {
            $results['items'] = $result;
            $results['_meta'] = [
                'page' => $collection->getCurPage(),
                'pagesize' => $collection->getPageSize(),
                'total' => $collection->getSize(),
                'totalpage' => $collection->getLastPageNumber()
            ];
        } else {
            $results = $result;
        }

        return $this->setData(self::RESULT, $results);
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        $code = $this->getData(self::CODE);

        return $code ?: 200;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $message = $this->getData(self::MESSAGE);
        return $message ?: '';
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        $result = $this->getData(self::RESULT);
        return $result ?: new \stdClass();
    }

    public function addError($code, $message)
    {
        $this->setCode($code);
        $this->setMessage($message);

        return $this;
    }
}