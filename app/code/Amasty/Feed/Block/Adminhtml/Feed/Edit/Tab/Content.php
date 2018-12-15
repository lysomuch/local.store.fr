<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Block\Adminhtml\Feed\Edit\Tab;

use Amasty\Feed\Model\Export\Product as ExportProduct;
use Magento\Backend\Block\Widget;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Content extends Widget implements RendererInterface
{
    protected $_export;
    protected $_category;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ExportProduct $export,
        \Amasty\Feed\Model\Category $_category,
        array $data = []
    ) {
        $this->_export = $export;
        $this->_category = $_category;

        parent::__construct($context, $data);
    }


    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getFormats()
    {
        return [
            'as_is' => 'As Is',
            'date' => 'Date',
            'price' => 'Price',
        ];
    }

    public function getParentsVars()
    {
        return [
            'no' => 'No',
            'yes' => 'Yes',
        ];
    }

    public function getInventoryAttributes()
    {
        // all inventory qty,min_qty,use_config_min_qty,is_qty_decimal,backorders,use_config_backorders,min_sale_qty,
        // use_config_min_sale_qty,max_sale_qty,use_config_max_sale_qty,is_in_stock,notify_stock_qty,
        // use_config_notify_stock_qty,manage_stock,use_config_manage_stock,use_config_qty_increments,qty_increments,
        // use_config_enable_qty_inc,enable_qty_increments,is_decimal_divided,website_id

        return [
            ExportProduct::PREFIX_INVENTORY_ATTRIBUTE . '|qty' => 'Qty',
            ExportProduct::PREFIX_INVENTORY_ATTRIBUTE . '|is_in_stock' => 'Is In Stock',

//            \Amasty\Feed\Model\Export\Product::PREFIX_INVENTORY_ATTRIBUTE . '|backorders' => 'Allow Backorders',
//            \Amasty\Feed\Model\Export\Product::PREFIX_INVENTORY_ATTRIBUTE . '|min_qty' => 'Out Of Stock Qty',
//            \Amasty\Feed\Model\Export\Product::PREFIX_INVENTORY_ATTRIBUTE . '|min_sale_qty' => 'Min Cart Qty',
//            \Amasty\Feed\Model\Export\Product::PREFIX_INVENTORY_ATTRIBUTE . '|max_sale_qty' => 'Max Cart Qty',
//            \Amasty\Feed\Model\Export\Product::PREFIX_INVENTORY_ATTRIBUTE . '|notify_stock_qty' => 'Notify On Stock Below'
        ];
    }

    public function getBasicAttributes()
    {
        return [
            ExportProduct::PREFIX_BASIC_ATTRIBUTE . '|sku' => 'SKU',
            ExportProduct::PREFIX_BASIC_ATTRIBUTE . '|product_type' => 'Type',
            ExportProduct::PREFIX_BASIC_ATTRIBUTE . '|product_websites' => 'Websites',
            ExportProduct::PREFIX_BASIC_ATTRIBUTE . '|created_at' => 'Created',
            ExportProduct::PREFIX_BASIC_ATTRIBUTE . '|updated_at' => 'Updated',
            ExportProduct::PREFIX_BASIC_ATTRIBUTE . '|product_id' => 'Product ID',
//            \Amasty\Feed\Model\Export\Product::PREFIX_BASIC_ATTRIBUTE . '|store_id' => 'Store Id',
        ];
    }

    public function getCategoryAttributes()
    {
        $attr = [
            ExportProduct::PREFIX_CATEGORY_ATTRIBUTE . '|category' => 'Default',
        ];

        foreach ($this->_category->getSortedCollection() as $category) {
            $attr[ExportProduct::PREFIX_MAPPED_CATEGORY_ATTRIBUTE . '|' . $category->getCode()] = $category->getName();
        }
        
        return $attr;
    }

    public function getCategoryPathsAttributes()
    {
        $attr = [
            ExportProduct::PREFIX_CATEGORY_PATH_ATTRIBUTE . '|category' => 'Default',
        ];

        foreach ($this->_category->getSortedCollection() as $category) {
            $attr[ExportProduct::PREFIX_MAPPED_CATEGORY_PATHS_ATTRIBUTE . '|'.$category->getCode()] = $category->getName();
        }
        
        return $attr;
    }

    public function getImageAttributes()
    {
        return [
            ExportProduct::PREFIX_IMAGE_ATTRIBUTE . '|thumbnail'   => 'Thumbnail',
            ExportProduct::PREFIX_IMAGE_ATTRIBUTE . '|image'       => 'Base Image',
            ExportProduct::PREFIX_IMAGE_ATTRIBUTE . '|small_image' => 'Small Image',
        ];
    }

    public function getGalleryAttributes()
    {
        return [
            ExportProduct::PREFIX_GALLERY_ATTRIBUTE . '|image_1' => 'Image 1',
            ExportProduct::PREFIX_GALLERY_ATTRIBUTE . '|image_2' => 'Image 2',
            ExportProduct::PREFIX_GALLERY_ATTRIBUTE . '|image_3' => 'Image 3',
            ExportProduct::PREFIX_GALLERY_ATTRIBUTE . '|image_4' => 'Image 4',
            ExportProduct::PREFIX_GALLERY_ATTRIBUTE . '|image_5' => 'Image 5',
        ];
    }

    public function getPriceAttributes()
    {
        return [
            ExportProduct::PREFIX_PRICE_ATTRIBUTE . '|price'           => 'Price',
            ExportProduct::PREFIX_PRICE_ATTRIBUTE . '|final_price'     => 'Final Price',
            ExportProduct::PREFIX_PRICE_ATTRIBUTE . '|min_price'       => 'Min Price',
            ExportProduct::PREFIX_PRICE_ATTRIBUTE . '|max_price'       => 'Max Price',
            ExportProduct::PREFIX_PRICE_ATTRIBUTE . '|tax_price'       => 'Price with TAX(VAT)',
            ExportProduct::PREFIX_PRICE_ATTRIBUTE . '|tax_final_price' => 'Final Price with TAX(VAT)',
        ];
    }

    public function getUrlAttributes()
    {
        return [
            ExportProduct::PREFIX_URL_ATTRIBUTE . '|short'         => 'Short',
            ExportProduct::PREFIX_URL_ATTRIBUTE . '|with_category' => 'With Category',
        ];
    }

    public function getProductAttributes()
    {
        $attributes = [];
        $codes = $this->_export->getExportAttrCodesList();

        foreach ($codes as $code => $title) {
            $attributes[ExportProduct::PREFIX_PRODUCT_ATTRIBUTE . "|" . $code] = $title;
        }

        return $attributes;
    }

    public function getModiftVars()
    {
        $ret = [
            'strip_tags' => 'Strip Tags',
            'html_escape' => 'Html Escape',
            'lowercase' => 'Lowercase',
            'integer' => 'Integer',
            'length' => 'Length',
            'prepend' => 'Prepend',
            'append' => 'Append',
            'replace' => 'Replace'
        ];
        
        return $ret;
    }

    public function getArgs()
    {
        $args = [
            'replace' => [
                __('From'),
                __('To'),
            ],
            'prepend' => [
                __('Text'),
            ],
            'append'  => [
                __('Text'),
            ],
            'length'  => [
                __('Max Length'),
            ],
        ];

        return $args;
    }

    public function escapeOption($text)
    {
        return $this->escapeJsQuote($this->escapeHtml($text));
    }
}
