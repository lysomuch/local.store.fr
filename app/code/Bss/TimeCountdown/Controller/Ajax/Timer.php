<?php
namespace Bss\TimeCountdown\Controller\Ajax;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
 
class Timer extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Bss\TimeCountdown\Helper\ProductData
     */
    private $helperProduct;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $datetime;

    private $resultJsonFactory;

    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Bss\TimeCountdown\Helper\ProductData $helperProduct,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->helperProduct = $helperProduct;
        $this->datetime = $date;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            if ($this->getRequest()->getParam('product_id')) {
                $productId = $this->getRequest()->getParam('product_id');
                $displayType = $this->getRequest()->getParam('display_type');
                $product = $this->productRepository->getById($productId);
                if ($displayType === 'product') {
                    $infoCountdown = $this->helperProduct->getInfoDisplayProductPage($product);
                } else {
                    $infoCountdown = $this->helperProduct->getInfoDisplayCatalogPage($product);
                }
                $result  = $infoCountdown['time_rest'];
                return $resultJson->setData($result);
            } else {
                $timeRest = $this->getRequest()->getParam('time_rest');
                $dateTimeZone = $this->datetime->date()->format('Y-m-d H:i:s');
                $timeZone = strtotime($dateTimeZone);
                $result = $timeRest - $timeZone;
                if($result > 0) {
                    return $resultJson->setData($result);
                }
                return $resultJson->setData(null);
           }
        } else {
            return $resultJson->setData(null);
        }
    }
}
