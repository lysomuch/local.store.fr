<?php


namespace Silk\Webapi\Framework\Webapi;


class ServiceOutputProcessor extends \Magento\Framework\Webapi\ServiceOutputProcessor
{
    public function process($data, $serviceClassName, $serviceMethodName)
    {
        /** @var string $dataType */
        $dataType = $this->methodsMapProcessor->getMethodReturnType($serviceClassName, $serviceMethodName);
        if ($dataType === '\Silk\Webapi\Api\Data\ResultInterface') {
            /** @var \Silk\Webapi\Api\Data\ResultInterface $data */
            return [
                'code' => $data->getCode(),
                'message' => $data->getMessage(),
                'result' => $data->getResult()
            ];
        } else {
            return $this->convertValue($data, $dataType);
        }
    }
}