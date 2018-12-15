<?php
namespace Silk\Cms\Model;


use Magento\Setup\Exception;

class DealerWholesale extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context, $registry);
    }

    /**
     * 发邮件
     * @param array $tpl_vars 模板变量数组
     * @return bool
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendEmail($tpl_vars=[])
    {
        $receiver_email = $this->_getConfig('email');
        $receiver_name = $this->_getConfig('name');

        //增加模板变量
        $tpl_vars['recipient'] = $receiver_name;
        $tpl_vars['store'] = $this->_storeManager->getStore();

        try {
            $this->inlineTranslation->suspend();
            $store = $this->_storeManager->getStore()->getId();
            $transport = $this->_transportBuilder->setTemplateIdentifier('dealer_wholesale_email_template')
                ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                ->setTemplateVars($tpl_vars)
                ->setFrom('general')
                ->addTo($receiver_email, $receiver_name)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

            return true;
        }catch (\Magento\Framework\Exception\MailException $e) {
            return false;
        }
    }

    //获取系统后台配置
    protected function _getConfig($field) {
        return $this->scopeConfig->getValue(
            'trans_email/dealer_wholesale/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}