<?php
namespace Dotsquares\Imexport\Block\Adminhtml\Orders;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
	
    protected function _construct()
    {
        parent::_construct();
        $this->setId('dotsquares_items_form');
        $this->setTitle(__('Item Information'));
    }

    protected function _prepareForm()
    {
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'order_form',
                    'action' => $this->getUrl('*/*/importallOrders'),
                    'method' => 'post',
					'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $fieldset = $form->addFieldset('imports_form', array('legend'=>__('Import Orders')));
        $fieldset->addField('store_id', 'select', array(
        	'name' => 'store_id',
        	'label' =>__('Store View'),
        	'title' =>__('Store View'),
        	'required' => true,
        	'values' => $object_manager->create('Dotsquares\Imexport\Model\Functional\Export')->getStoreIds(),
        ));
        $fieldset->addField('import_limit', 'select', array(
        	'label'     =>__('Order Import Limit'),
        	'name'      => 'import_limit',
        	'required' => true,
        	'values'    => array(
        		array(
        			'value'     => '25',
        			'label'     =>__('25'),
        		),
        		array(
        			'value'     => '50',
        			'label'     =>__('50'),
        		),
        		array(
        			'value'     => '100',
        			'label'     =>__('100'),
        		),
        		array(
        			'value'     => '150',
        			'label'     =>__('150'),
        		),
        		array(
        			'value'     => '200',
        			'label'     =>__('200'),
        		),
                array(
                    'value'     => '600',
                    'label'     =>__('600'),
                ),
        	),
        ));	
        
        $fieldset->addField('order_csv', 'file', array(
        	'label'     =>__('CSV File : '),
        	'required'  => true,
        	'name'      => 'order_csv',
        	'after_element_html' => '</br>Note : <small>use the csv file which has been export by the same module.</small>',
        ));
        
        $fieldset->addField('submit', 'submit', array(
        	'value'  => 'Import',
        	'after_element_html' => '<small></small>',
        	'class' => 'form-button', 			  
        	'tabindex' => 1
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}