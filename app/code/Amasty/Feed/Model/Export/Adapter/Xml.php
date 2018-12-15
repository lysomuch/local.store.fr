<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model\Export\Adapter;

class Xml extends \Amasty\Feed\Model\Export\Adapter\Csv
{
    protected $_fileHandler;
    protected $_header;
    protected $_item;
    protected $_content;
    protected $_contentAttributes;
    protected $_footer;

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'text/xml';
    }

    /**
     * Return file extension for downloading.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return 'xml';
    }


    /**
     * Write header
     *
     * @return $this
     */
    public function writeHeader()
    {
        if (!empty($this->_header)) {
            $header = str_replace(
                '<created_at>{{DATE}}</created_at>',
                '<created_at>' . date('Y-m-d H:i') . '</created_at>',
                $this->_header
            );
            $this->_fileHandler->write($header);
        }

        return $this;
    }

    /**
     * Write footer
     *
     * @return $this
     */
    public function writeFooter()
    {
        if (!empty($this->_footer)) {
            $this->_fileHandler->write($this->_footer);
        }

        return $this;
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @throws \Exception
     * @return $this
     */
    public function writeDataRow(array &$rowData)
    {
        $replace = [];
        if (is_array($this->_contentAttributes)) {
            foreach ($this->_contentAttributes as $search => $attribute) {

                $code = array_key_exists('parent', $attribute) && $attribute['parent'] == 'yes' ?
                    $attribute['attribute'] . '|parent' :
                    $attribute['attribute'];

                $value = $this->_modifyValue($attribute, isset($rowData[$code]) ? $rowData[$code] : '');
                $value = $this->_formatValue($attribute, $value);

                $replace['{' . $search . '}'] = $value;
            }
        }

        $write = '';

        if ($this->_item) {
            $write .= '<' . $this->_item . '>';
        }

        $writeItem = strtr($this->_content, $replace);

        $this->clearEmptyTag($writeItem, 'g:additional_image_link');
        $this->clearEmptyTag($writeItem, 'g:sale_price_effective_date');

        $write .= $writeItem;

        if ($this->_item) {
            $write .= '</' . $this->_item . '>';
        }

        $this->_fileHandler->write($write);

        return $this;
    }

    /**
     * Modify value in field
     *
     * @param array $field
     * @param mixed $value
     * @return string
     */
    protected function _modifyValue($field, $value)
    {
        if ($field['modify'] != '') {
            foreach (explode('|', $field['modify']) as $modify) {
                $modifyArr = explode(":", $modify, 2);

                $modifyType = $modifyArr[0];
                $arg0 = null;
                $arg1 = null;

                if (isset($modifyArr[1])) {
                    $modifyArgs = explode("^", $modifyArr[1]);
                    if (isset($modifyArgs[0])) {
                        $arg0 = $modifyArgs[0];
                    }

                    if (isset($modifyArgs[1])) {
                        $arg1 = $modifyArgs[1];
                    }
                }

                $value = $this->_modify($value, $modifyType, $arg0, $arg1);
            }
        }

        return $value;
    }

    /**
     * Add CDATA
     *
     * @param array $field
     * @param mixed $value
     * @return string
     */
    protected function _formatValue($field, $value)
    {
        $ret = parent::_formatValue($field, $value);

        if (!empty($field['modify']) && !empty($ret)) {
            $ret = '<![CDATA[' . $ret . ']]>';
        }

        return $ret;
    }

    /**
     * Init feed
     *
     * @param \Amasty\Feed\Model\Feed $feed
     * @return $this
     */
    public function initBasics($feed)
    {
        parent::initBasics($feed);

        $this->_header = $feed->getXmlHeader();
        $this->_item = $feed->getXmlItem();
        $this->_footer = $feed->getXmlFooter();

        $this->_parseContent($feed->getXmlContent());

        return $this;
    }

    /**
     * Parse content of feed
     *
     * @param string $content
     * @return void
     */
    protected function _parseContent($content)
    {
        $regex = "#{(.*?)}#";

        preg_match_all($regex, $content, $vars);

        $contentAttributes = [];

        if (isset($vars[1])) {

            foreach ($vars[1] as $attributeRow) {
                $attributeParams = [];

                preg_match("/attribute=\"(.*?)\"/", $attributeRow, $attrReg);
                preg_match("/format=\"(.*?)\"/", $attributeRow, $formatReg);
                preg_match("/modify=\"(.*?)\"/", $attributeRow, $lengthReg);
                preg_match("/parent=\"(.*?)\"/", $attributeRow, $parentReg);
                
                if (isset($attrReg[1])) {

                    list($type, $code) = explode("|", $attrReg[1]);

                    $attributeParams = [
                        'attribute' => isset($attrReg[1]) ? $attrReg[1] : '',
                        'format' => isset($formatReg[1]) ? $formatReg[1] : 'as_is',
                        'modify' => isset($lengthReg[1]) ? $lengthReg[1] : '',
                        'parent' => isset($parentReg[1]) ? $parentReg[1] : 'no',
                    ];
                }

                $contentAttributes[$attributeRow] = $attributeParams;
            }
        }

        $this->_content = $content;
        $this->_contentAttributes = $contentAttributes;
    }

    /**
     * Clear empty tag
     *
     * @param string &$content
     * @param string $tag
     * @return void
     */
    protected function clearEmptyTag(&$content = '', $tag = '')
    {
        $pattern = '<' . $tag . '></' . $tag . '>' . PHP_EOL;
        $content = str_replace($pattern, '', $content);
    }
}
