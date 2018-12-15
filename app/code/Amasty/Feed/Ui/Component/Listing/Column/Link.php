<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Ui\Component\Listing\Column;


class Link extends Action
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $link = $this->_getDownloadHref($item['filename'], $item['store_id']);
                    $item[$this->getData('name')] = $item['generated_at'] ? '<span class="amasty-copy-on-clipboard-text">' . $link . '</span>' . "\n" . $this->makeCopyToClipboardButton() : '';
                }
            }
        }

        return $dataSource;
    }

    /**
     * @return string
     */
    protected function makeCopyToClipboardButton()
    {
        return '<button class="button action primary amasty-copy-on-clipboard-button">' . __('Copy') . '</button>';
    }
}
