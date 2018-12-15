<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model\Export\Adapter;

use Magento\Framework\Exception\LocalizedException;

class Csv extends \Magento\ImportExport\Model\Export\Adapter\Csv
{
    protected $_csvField = [];
    protected $_columnName;
    protected $_header;

    protected $_storeManager;
    protected $_currencyFactory;
    protected $_rates;

    protected $_formatPriceCurrency;
    protected $_formatPriceCurrencyShow;
    protected $_formatPriceDecimals;
    protected $_formatPriceDecimalPoint;
    protected $_formatPriceThousandsSeparator;

    protected $_formatDate;
    protected $_page;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        $destination = null,
        $page = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_currencyFactory = $currencyFactory;
        $this->_page = $page;

        parent::__construct($filesystem, $destination);
    }

    protected function _init()
    {
        $mode = $this->_page == 0 ? 'w' : 'a';

        $this->_fileHandler = $this->_directoryHandle->openFile($this->_destination, $mode);
        return $this;
    }

    public function initBasics($feed)
    {
        $enclosure = $feed->getCsvEnclosure();
        $delimiter = $feed->getCsvDelimiter();

        $enclosures = [
            'double_quote' => '"',
            'quote'        => '\'',
            'space'        => ' ',
            'none'         => '/n'
        ];

        $this->_enclosure = isset($enclosures[$enclosure]) ? $enclosures[$enclosure] : '"';

        $delimiters = [
            'comma'     => ',',
            'semicolon' => ';',
            'pipe'      => '|',
            'tab'       => chr(9)
        ];

        $this->_delimiter = isset($delimiters[$delimiter]) ? $delimiters[$delimiter] : ',';

        $this->_columnName = $feed->getCsvColumnName() == 1;

        $this->_header = $feed->getCsvHeader();

        $this->_csvField = $feed->getCsvField();

        $this->initPrice($feed);

        return $this;
    }

    public function initPrice($feed)
    {
        $decimals = [
            'one'   => 1,
            'two'   => 2,
            'three' => 3,
            'four'  => 4
        ];

        $separators = [
            'dot'   => '.',
            'comma' => ',',
            'space' => ' ',
        ];

        $formatPriceDecimals = $feed->getFormatPriceDecimals();
        $formatPriceDecimalPoint = $feed->getFormatPriceDecimalPoint();
        $formatPriceThousandsSeparator = $feed->getFormatPriceThousandsSeparator();
        $formatDate = $feed->getFormatDate();

        $this->_formatPriceCurrency = $feed->getFormatPriceCurrency();
        $this->_formatPriceCurrencyShow = $feed->getFormatPriceCurrencyShow() == 1;

        $this->_formatPriceDecimals = isset($decimals[$formatPriceDecimals]) ? $decimals[$formatPriceDecimals] : 2;
        $this->_formatPriceDecimalPoint = isset($separators[$formatPriceDecimalPoint]) ? $separators[$formatPriceDecimalPoint] : '.';

        $this->_formatPriceThousandsSeparator = isset($separators[$formatPriceThousandsSeparator]) ? $separators[$formatPriceThousandsSeparator] : ',';

        $this->_formatDate = !empty($formatDate) ? $formatDate : "Y-m-d";
    }

    protected function _getFieldKey($field)
    {
        $postfix = isset($field['parent']) && $field['parent'] == 'yes' ? '|parent' : '';
        return $field['attribute'] . $postfix;
    }

    public function writeHeader()
    {
        $columns = [];

        foreach ($this->_csvField as $idx => $field) {
            $this->_headerCols[$idx . "_idx"] = false;
            $columns[] = $field['header'];
        }

        if (!empty($this->_header)) {
            $this->_fileHandler->write($this->_header . "\n");
        }

        if ($this->_columnName !== FALSE) {
            $this->_fileHandler->writeCsv($columns, $this->_delimiter, $this->_enclosure);
        }

        return $this;
    }

    public function writeFooter()
    {

    }

    public function setHeaderCols(array $headerColumns)
    {
        if (null !== $this->_headerCols) {
            throw new LocalizedException(__('The header column names are already set.'));
        }
        if ($headerColumns) {
            foreach ($headerColumns as $columnName) {
                $this->_headerCols[$columnName] = false;
            }
        }
        return $this;
    }

    public function writeDataRow(array &$rowData)
    {
        $writeRow = [];

        foreach ($this->_csvField as $idx => $field) {
            if ($field['static_text']) {
                $value = $field['static_text'];
            } else {
                $fieldKey = $this->_getFieldKey($field);
                $value = isset($rowData[$fieldKey]) ? $rowData[$fieldKey] : '';
            }

            $value = $this->_modifyValue($field, $value);
            $value = $this->_formatValue($field, $value);

            $writeRow[$idx . "_idx"] = $value;
        }

        if (count($writeRow) > 0) {
            if ($this->_enclosure == '/n') {
                foreach ($writeRow as $inx => $val) {
                    $writeRow[$inx] = str_replace($this->_delimiter, "", $val);
                }
                $this->_fileHandler->write(implode($this->_delimiter, $writeRow) . "\n");

            } else {
                parent::writeRow($writeRow);
            }
        }

        return $this;
    }

    protected function _modifyValue($field, $value)
    {
        if (isset($field['modify']) && is_array($field['modify'])) {
            foreach ($field['modify'] as $modify) {

                $value = $this->_modify($value, $modify['modify'],
                    isset($modify['arg0']) ? $modify['arg0'] : null,
                    isset($modify['arg1']) ? $modify['arg1'] : null
                );
            }
        }

        return $value;
    }

    protected function _modify($value, $modify, $arg0 = null, $arg1 = null)
    {
        switch ($modify) {
            case "strip_tags":
                $value = strtr($value, ["\n" => '', "\r" => '']);
                $value = strip_tags($value);
                break;
            case "html_escape":
                $value = htmlspecialchars($value);
                break;
            case "lowercase":
                $value = function_exists("mb_strtolower") ?
                    mb_strtolower($value, "UTF-8") :
                    strtolower($value);
                break;
            case "integer":
                $value = intval($value);
                break;
            case 'length':
                $length = intval($arg0);

                if ($arg0 != '') {
                    $value = function_exists("mb_substr") ?
                        mb_substr($value, 0, $length, "UTF-8") :
                        substr($value, 0, $length);
                }
                break;
            case "prepend":
                $value = $arg0 . $value;
                break;
            case "append":
                $value .= $arg0;
                break;
            case "replace":
                $value = str_replace($arg0, $arg1, $value);
                break;
        }
        return $value;
    }

    protected function _formatValue($field, $value)
    {
        $format = isset($field['format']) ? $field['format'] : 'as_is';
        $length = isset($field['length']) ? $field['length'] : '';

        switch ($format) {
            case 'as_is':
                break;
            case 'date':
                if (!empty($value))
                    $value = date($this->_formatDate, strtotime($value));
                break;
            case 'price':

                if ($value > 0) {
                    $value = $value * $this->getCurrencyRate();
                    $value = number_format($value, $this->_formatPriceDecimals, $this->_formatPriceDecimalPoint, $this->_formatPriceThousandsSeparator);

                    if ($this->_formatPriceCurrencyShow && $this->_formatPriceCurrency) {
                        $value .= ' ' . $this->_formatPriceCurrency;
                    }
                }

                break;
            case 'integer':

                break;
        }

        return $value;
    }

    protected function getCurrencyRate()
    {
        if (!$this->_rates) {
            $codes = $this->_storeManager->getStore()->getAvailableCurrencyCodes(true);
            $rates = $this->_currencyFactory->create()->getCurrencyRates(
                $this->_storeManager->getStore()->getBaseCurrency(),
                $codes
            );
        }

        return isset($rates[$this->_formatPriceCurrency]) ? $rates[$this->_formatPriceCurrency] : 1;
    }
}
