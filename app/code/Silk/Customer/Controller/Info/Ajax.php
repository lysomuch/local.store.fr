<?php
namespace Silk\Customer\Controller\Info;


use Magento\Framework\App\ResponseInterface;

class Ajax extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Customer\Model\Session $customerSession */
    protected $customerSession;

    /** @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory */
    protected $jsonEncoder;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $date;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->jsonEncoder = $resultJsonFactory;
        $this->date =  $date;
    }

    /**
     * 获取已登录用户的基本信息，暂只获取姓名和当前服务器时间
     * @return json
     */
    public function execute()
    {
        $resultJson = $this->jsonEncoder->create();

        $data = ['name' => '', 'currentServerTime'=>$this->date->date()->format('Y-m-d H:i:s')];

        if( $this->customerSession->isLoggedIn() ) { //已登录
            //获取用户所有session数据
//            $data = $this->customerSession->getCustomer()->getdata();
            $data['name'] = $this->customerSession->getCustomer()->getName();
        }

        return $resultJson->setJsonData(json_encode($data));
    }
}