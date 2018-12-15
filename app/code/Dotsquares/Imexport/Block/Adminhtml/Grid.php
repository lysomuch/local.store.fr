<?php 
namespace Dotsquares\Imexport\Block\Adminhtml;

class Grid  extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'imexport_export_orders';
        $this->_blockGroup = 'Dotsquares_Imexport';
        $this->_headerText = __('Order Grid');
        parent::_construct();
        $this->removeButton('add');
    }
}