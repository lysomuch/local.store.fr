<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model;

use Amasty\Feed\Model\Export\Product;
use Amasty\Feed\Model\Indexer\Feed\FeedRuleProcessor;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\ImportExport\Model\Export\Adapter\AbstractAdapter;

/**
 * @method int getExcludeDisabled
 * @method int getExcludeOutOfStock
 * @method int getExcludeNotVisible
 */
class Feed extends AbstractModel
{
    const COMPRESS_NONE = '';

    const COMPRESS_ZIP = 'zip';

    const COMPRESS_GZ = 'gz';

    const COMPRESS_BZ = 'bz2';

    const DELIVERY_TYPE_DLD = 0;

    const DELIVERY_TYPE_FTP = 1;

    const DELIVERY_TYPE_SFTP = 2;

    protected $_export;

    protected $_rule;

    protected $_compressorFactory;

    protected $_writer;

    protected $_exportConfig = [
        'csv' => 'Amasty\Feed\Model\Export\Adapter\Csv',
        'txt' => 'Amasty\Feed\Model\Export\Adapter\Csv',
        'xml' => 'Amasty\Feed\Model\Export\Adapter\Xml'
    ];

    protected $_objectManager;

    protected $_filesystem;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Amasty\Feed\Model\Validator
     */
    private $validator;

    /**
     * @var \Amasty\Feed\Model\Indexer\Feed\FeedRuleProcessor
     */
    private $feedRuleProcessor;

    /**
     * @var \Magento\Framework\Filesystem\Io\Ftp\Proxy
     */
    private $ftp;

    /**
     * @var \Magento\Framework\Filesystem\Io\Sftp\Proxy
     */
    private $sftp;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $metadata;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Feed\Model\ResourceModel\Feed $resource = null,
        \Amasty\Feed\Model\ResourceModel\Feed\Collection $resourceCollection = null,
        \Magento\Framework\Filesystem\Io\Ftp\Proxy $ftp,
        \Magento\Framework\Filesystem\Io\Sftp\Proxy $sftp,
        \Magento\Framework\App\ProductMetadataInterface $metadata,
        Product $export,
        \Amasty\Feed\Model\Rule $rule,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem $filesystem,
        CompressorFactory $compressorFactory,
        \Amasty\Base\Model\Serializer $serializer,
        Validator $validator,
        FeedRuleProcessor $feedRuleProcessor,
        array $data = []
    ) {
        $this->_export = $export;
        $this->_rule = $rule;
        $this->_objectManager = $objectManager;
        $this->_filesystem = $filesystem;
        $this->_compressorFactory = $compressorFactory;
        $this->serializer = $serializer;
        $this->validator = $validator;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->feedRuleProcessor = $feedRuleProcessor;
        $this->ftp = $ftp;
        $this->sftp = $sftp;
        $this->metadata = $metadata;
    }

    /**
     * @return \Amasty\Feed\Model\Feed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareModelConditions()
    {
        $this->setCollectedAttributes([]);
        $this->getRule()->setConditions([]);
        $this->getRule()->setConditionsSerialized($this->getConditionsSerialized());
        $this->getRule()->setStoreId($this->getStoreId());

        return $this;
    }

    /**
     * @param int $page
     * @param int $itemsPerPage
     * @param array $ids
     *
     * @return array
     */
    public function getValidProducts($page, $itemsPerPage, array $ids = [])
    {
        return $this->validator->getValidProducts($this, $page, $itemsPerPage, $ids);
    }

    /**
     * @return \Amasty\Feed\Model\Rule
     */
    public function getRule()
    {
        return $this->_rule;
    }

    /**
     * @param \Amasty\Feed\Model\Rule $rule
     */
    public function setRule($rule)
    {
        $this->_rule = $rule;
    }

    protected function _construct()
    {
        $this->_init('Amasty\Feed\Model\ResourceModel\Feed');
        $this->setIdFieldName('entity_id');
    }

    public function saveFeedData()
    {
        $this->_resource->save($this);

        if (!$this->feedRuleProcessor->getIndexer(FeedRuleProcessor::INDEXER_ID)->isScheduled()) {
            $this->feedRuleProcessor->reindexRow($this->getId());
        }
    }

    /**
     * @param int $feedId
     *
     * @return $this
     */
    public function loadByFeedId($feedId)
    {
        $this->_resource->loadByFeedId($this, $feedId);

        return $this;
    }

    /**
     * @return bool
     */
    public function isCsv()
    {
        return $this->getFeedType() == 'txt' || $this->getFeedType() == 'csv';
    }

    /**
     * @return bool
     */
    public function isXml()
    {
        return $this->getFeedType() == 'xml';
    }

    /**
     * @return array
     */
    public function getCsvField()
    {
        $ret = parent::getCsvField();

        if (!is_array($ret)) {
            $config = $this->serializer->unserialize($ret);
            $ret = [];

            if (is_array($config)) {
                foreach ($config as $item) {
                    $ret[] = [
                        'header' => isset($item['header']) ? $item['header'] : '',
                        'attribute' => isset($item['attribute']) ? $item['attribute'] : null,
                        'static_text' => isset($item['static_text']) ? $item['static_text'] : null,
                        'format' => isset($item['format']) ? $item['format'] : '',
                        'parent' => isset($item['parent']) ? $item['parent'] : '',
                        'modify' => isset($item['modify']) ? $item['modify'] : [],
                    ];
                }
            }
        }

        return $ret;
    }

    public function getFileFormat()
    {
        return $this->isCsv() ? 'csv' : $this->getFeedType();
    }

    /**
     * @return AbstractAdapter
     * @throws LocalizedException
     */
    protected function _getWriter()
    {
        if (!$this->_writer) {
            try {
                $this->_writer = $this->_objectManager->create(
                    $this->_exportConfig[$this->getFeedType()],
                    [
                        'destination' => $this->getFilename(),
                        'page' => $this->_export->getPage()
                    ]
                )->initBasics($this);

            } catch (\Exception $e) {
                $this->_logger->critical($e);
                throw new LocalizedException(__('Please correct the file format.'));
            }

            if (!$this->_writer instanceof AbstractAdapter) {
                throw new LocalizedException(
                    __(
                        'The adapter object must be an instance of %1.',
                        'Magento\ImportExport\Model\Export\Adapter\AbstractAdapter'
                    )
                );
            }
        }

        return $this->_writer;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->_getWriter()->getContentType();
    }

    /**
     * @param bool $parent
     *
     * @return array
     */
    protected function _getAttributes($parent = false)
    {
        $attributes = [
            Product::PREFIX_BASIC_ATTRIBUTE => [],
            Product::PREFIX_PRODUCT_ATTRIBUTE => [],
            Product::PREFIX_INVENTORY_ATTRIBUTE => [],
            Product::PREFIX_PRICE_ATTRIBUTE => [],
            Product::PREFIX_CATEGORY_ATTRIBUTE => [],
            Product::PREFIX_CATEGORY_PATH_ATTRIBUTE => [],
            Product::PREFIX_MAPPED_CATEGORY_ATTRIBUTE => [],
            Product::PREFIX_MAPPED_CATEGORY_PATHS_ATTRIBUTE => [],
            Product::PREFIX_IMAGE_ATTRIBUTE => [],
            Product::PREFIX_GALLERY_ATTRIBUTE => [],
            Product::PREFIX_URL_ATTRIBUTE => [],
            Product::PREFIX_OTHER_ATTRIBUTES => []

        ];

        if ($this->isCsv()) {
            $this->processingCsv($attributes, $parent);
        } elseif ($this->isXml()) {
            $this->processingXml($attributes, $parent);
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @param bool $parent
     */
    private function processingCsv(&$attributes, $parent)
    {
        foreach ($this->getCsvField() as $field) {

            if (($parent && isset($field['parent']) && $field['parent'] == 'yes')
                || !$parent && isset($field['attribute'])
            ) {
                list($type, $code) = explode("|", $field['attribute']);

                if (array_key_exists($type, $attributes)) {
                    $attributes[$type][$code] = $code;
                }
            }
        }
    }

    /**
     * @param array $attributes
     * @param bool $parent
     */
    private function processingXml(&$attributes, $parent)
    {
        $regex = "#{(.*?)}#";

        preg_match_all($regex, $this->getXmlContent(), $vars);

        if (isset($vars[1])) {

            foreach ($vars[1] as $attributeRow) {
                preg_match("/attribute=\"(.*?)\"/", $attributeRow, $attrReg);
                preg_match("/parent=\"(.*?)\"/", $attributeRow, $parentReg);

                if (isset($attrReg[1])) {
                    list($type, $code) = explode("|", $attrReg[1]);
                    $attributeParent = isset($parentReg[1]) ? $parentReg[1] : 'no';

                    if (($parent && $attributeParent == 'yes') || !$parent) {
                        if (array_key_exists($type, $attributes)) {
                            $attributes[$type][$code] = $code;
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getMatchingProductIds()
    {
        $this->_rule->setConditions([]);
        $this->_rule->setConditionsSerialized($this->getConditionsSerialized());
        $this->_rule->setStoreId($this->getStoreId());

        return array_keys($this->_rule->getFeedMatchingProductIds());
    }

    /**
     * @return array
     */
    protected function getUtmParams()
    {
        $ret = [];

        if ($this->getUtmSource()) {
            $ret['utm_source'] = $this->getUtmSource();
        }

        if ($this->getUtmMedium()) {
            $ret['utm_medium'] = $this->getUtmMedium();
        }

        if ($this->getUtmTerm()) {
            $ret['utm_term'] = $this->getUtmTerm();
        }

        if ($this->getUtmContent()) {
            $ret['utm_content'] = $this->getUtmContent();
        }

        if ($this->getUtmCampaign()) {
            $ret['utm_campaign'] = $this->getUtmCampaign();
        }

        return $ret;
    }

    /**
     * @return Product
     */
    public function getExport()
    {
        return $this->_export;
    }

    /**
     * @param $page
     * @param $productIds
     * @param $lastPage
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function export($page, $productIds, $lastPage)
    {
        $result = $this->_export
            ->setPage($page)
            ->setWriter($this->_getWriter())
            ->setAttributes($this->_getAttributes())
            ->setParentAttributes($this->_getAttributes(true))
            ->setMatchingProductIds($productIds)
            ->setUtmParams($this->getUtmParams())
            ->setStoreId($this->getStoreId())
            ->export($lastPage);

        if ($this->getDeliveryEnabled() && $this->_export->getIsLastPage()) {
            switch ($this->getDeliveryType()) {
                case 'ftp':
                    $this->_ftpUpload();
                    break;
                case 'sftp':
                    $this->_sftpUpload();
                    break;
                default:
                    throw new LocalizedException(__('Invalid protocol'));
            }
        }

        $this->setGeneratedAt(date('Y-m-d H:i:s'));
        $this->save();

        return $result;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->_getWriter()->getContents();
    }

    /**
     * @return string
     */
    public function getMainPath()
    {
        return $this->_filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->getAbsolutePath() . '/'
            . $this->getOutputFilename();
    }


    /**
     * @return string
     */
    protected function _getRemotePath()
    {
        $remotePath = $this->getDeliveryPath();
        if ('/' != substr($remotePath, -1, 1) && '\\' != substr($remotePath, -1, 1)) {
            $remotePath .= '/';
        }
        $remoteFileName = substr($this->getMainPath(), strrpos($this->getMainPath(), '/') + 1);
        $remotePath .= $remoteFileName;

        return $remotePath;
    }

    protected function _ftpUpload()
    {
        if (strpos($this->getDeliveryHost(), ':') !== false) {
            list($host, $port) = explode(':', $this->getDeliveryHost(), 2);
        } else {
            $host = $this->getDeliveryHost();
            $port = null;
        }

        $this->ftp->open(
            [
                'host' => $host,
                'port' => $port,
                'user' => $this->getDeliveryUser(),
                'password' => $this->getDeliveryPassword(),
                'passive' => $this->getDeliveryPassiveMode(),
                'path' => $this->getDeliveryPath()
            ]
        );
        $this->ftp->write($this->getOutputFilename(), $this->getOutput()['content']);
        $this->ftp->close();
    }

    /**
     * @throws LocalizedException
     */
    protected function _sftpUpload()
    {
        if (version_compare($this->metadata->getVersion(), '2.2.0', '<')) {
            /** Fix for Magento <2.2.0 versions @see https://github.com/magento/magento2/issues/9016 */
            define('NET_SFTP_LOCAL_FILE', \phpseclib\Net\SFTP::SOURCE_LOCAL_FILE);
            define('NET_SFTP_STRING', \phpseclib\Net\SFTP::SOURCE_STRING);
        }

        $this->sftp->open(
            [
                'host' => $this->getDeliveryHost(),
                'username' => $this->getDeliveryUser(),
                'password' => $this->getDeliveryPassword(),
            ]
        );

        $path = $this->sftp->cd($this->getDeliveryPath() ? : '');

        if (!$path) {
            $this->sftp->close();
            throw new LocalizedException(__('Invalid path'));
        }

        $this->sftp->write($this->getOutputFilename(), $this->getOutput()['content']);
        $this->sftp->close();
    }

    /**
     * @return array
     */
    public function getTemplateOptionHash()
    {
        $ret = [];

        foreach ($this->getResourceCollection()->addFieldToFilter('is_template', 1) as $template) {
            $ret[$template->getId()] = $template->getName();
        }

        return $ret;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        $ret = parent::getFilename();
        $ext = '.' . $this->getFeedType();

        if (strpos($ret, $ext) === false) {
            $ret .= $ext;
        }

        return $ret;
    }

    /**
     * @return string
     */
    public function compress()
    {
        $filename = $this->getFilename();

        $dir = $this->_filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $outputFilename = $filename;
        $compressor = null;

        if ($this->getCompress() === self::COMPRESS_ZIP) {
            $compressor = $this->_compressorFactory->create([
                'compressor' => new \Magento\Framework\Archive\Zip
            ]);
        } elseif ($this->getCompress() === self::COMPRESS_GZ) {
            $compressor = $this->_compressorFactory->create([
                'compressor' => new \Magento\Framework\Archive\Gz
            ]);
        } elseif ($this->getCompress() === self::COMPRESS_BZ) {
            $compressor = $this->_compressorFactory->create([
                'compressor' => new \Magento\Framework\Archive\Bz
            ]);
        }

        if ($compressor) {
            $outputFilename .= '.' . $this->getCompress();
        }

        if ($compressor && $dir->isExist($filename)) {
            $compressor->pack(
                $dir->getAbsolutePath($filename),
                $dir->getAbsolutePath($outputFilename),
                $filename
            );

            $dir->delete($filename);
        }

        return $outputFilename;
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        $dir = $this->_filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);

        $outputFilename = $this->compress();

        $dirVarPath = $dir->getAbsolutePath();
        $filesystem = new \Zend_Search_Lucene_Storage_Directory_Filesystem($dirVarPath);

        return [
            'filename' => $outputFilename,
            'content' => $dir->readFile($outputFilename),
            'mtime' => $filesystem->fileModified($outputFilename)
        ];
    }

    /**
     * @return string
     */
    public function getOutputFilename()
    {
        $filename = $this->getFilename();

        $output = $this->getOutput();

        if (array_key_exists('filename', $output)) {
            $filename = $output['filename'];
        }

        return $filename;
    }

    public function getConditionsSerialized()
    {
        $conditionsSerialized = $this->getData('conditions_serialized');

        if ($conditionsSerialized) {
            if ($conditionsSerialized[0] == 'a') { // Old serialization format used
                if (interface_exists('\Magento\Framework\Serialize\SerializerInterface')) { // New version of Magento
                    $conditionsSerialized = $this->serializer->serialize(
                        $this->serializer->unserialize($conditionsSerialized)
                    );
                }
            }
        }

        return $conditionsSerialized;
    }
}
