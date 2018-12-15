<?php
namespace Silk\Cms\Controller\DealerWholesale;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Dosubmit extends Action
{
    /** @var PageFactory */
    protected $pageFactory;

    /** @var \Silk\Cms\Model\DealerWholesaleFactory */
    protected $model;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Silk\Cms\Model\DealerWholesaleFactory $modelFactory
    )
    {
        $this->pageFactory = $pageFactory;
        $this->model = $modelFactory->create();
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->pageFactory->create();

        //接收post过来的数据
        $post = $this->getRequest()->getPostValue();
        $data = isset($post['data']) ? $post['data'] : [];
        if( ! $data) return false;

        // 修改$data['sales_channels']数据结构
        if( isset($data['sales_channels']) && is_array($data['sales_channels']) ) {
            $sales_channels = '';
            foreach($data['sales_channels'] as $key=>$val) {
                if($key == 'other_value') {
                    $sales_channels .= ': ' . $val;
                }else {
                    $sales_channels .= ', ' . $val;
                }

                $data['sales_channels'] = substr($sales_channels, 1);
            }
        }else {
            $data['sales_channels'] = '';
        }

        //发邮件
        $bool = $this->model->sendEmail($data);

        if($bool) {
            $message = 'Submit successfully!';
            $this->messageManager->addSuccessMessage($message);
        }else {
            $message = 'Submit failure, please try again later.';
            $this->messageManager->addError($message);
        }

        //重定向
        $this->_redirect('olight-dealers-and-wholesales');
    }
}