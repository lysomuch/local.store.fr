<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductGridInlineEditor
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductGridInlineEditor\Controller\Adminhtml\InlineEditor;

class SaveMultiples extends \Bss\ProductGridInlineEditor\Controller\Adminhtml\InlineEditor
{
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'status' => 'error',
            ]);
        }
        
        try {
            $storeId        = $this->getStoreId();
            $product_skus   = $this->getSkus();
            $products_attributesData = $this->getAttributesData();
            $productIds     = array_keys($postItems);
            $skus_haschange = [];

            foreach ($products_attributesData as $productId => $attributesData) {
                $this->productAction->updateAttributes([$productId], $attributesData, $storeId);
                if (isset($product_skus[$productId])) {
                    $product = $this->getLoadProduct($productId);
                    $product_sku = $product_skus[$productId];
                    $original_sku = $product->getSku();
                    if ($product_sku != $original_sku) {
                        $_product = $this->_saveSku($product, $product_sku);
                        $skus_haschange[$product->getId()] = ['name' => $_product->getName(), 'sku' => $_product->getSku()];
                    }
                }
            }

            $this->updateInventory();
            $this->stockIndexerProcessor->reindexList($productIds);

            $message = __('A total of %1 record(s) were updated.', count($productIds));
            $status = 'success';

            $this->productFlatIndexerProcessor->reindexList($productIds);

            if ($this->catalogProduct->isDataForPriceIndexerWasChanged($attributesData)) {
                $this->productPriceIndexerProcessor->reindexList($productIds);
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $message = $e->getMessage();
            $status = 'error';
        } catch (\Exception $e) {
            $message = __('Something went wrong while updating the product(s) attributes.');
            $status = 'error';
        }

        return $resultJson->setData([
            'message' => $message,
            'status' => $status,
            'skus_haschange' => $skus_haschange
        ]);
    }
}
