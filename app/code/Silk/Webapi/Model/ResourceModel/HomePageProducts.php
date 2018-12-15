<?php
namespace Silk\Webapi\Model\ResourceModel;

class HomePageProducts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * 构造函数
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * 初始化资源模型
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'row_id');
    }

    /**
     * 通过传递id从DB搜索帖子标题。
     *
     * @param string $id
     * @return string|bool
     */
    public function getPostNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('post_id = :post_id');
        $binds = ['post_id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }

}