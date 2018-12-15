<?php 
namespace Dotsquares\Imexport\Model\Table;

class Billingtable extends \Magento\Framework\model\AbstractModel
{
    public function getbillingtable()
    {
        $table = array();
        $table = array(
            'customer_address_id',
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
            'street',
            'city',
            'region',
            'country_id',
            'postcode',
            'telephone',
            'company',
            'fax'
        );
        return $table;
    }
}