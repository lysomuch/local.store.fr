<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\LayeredNavigation\Helper\Mysql;

use Magento\Framework\App\ResourceConnection;

class Fulltext extends \Magento\Framework\DB\Helper\Mysql\Fulltext
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->connection = $resource->getConnection();
        parent::__construct($resource);
    }

    /**
     * Method for FULLTEXT search in Mysql, will generated MATCH ($columns) AGAINST ('$expression' $mode)
     *
     * @param string|string[] $columns Columns which add to MATCH ()
     * @param string $expression Expression which add to AGAINST ()
     * @param string $mode
     * @return string
     */
    public function getMatchQuery($columns, $expression, $mode = self::FULLTEXT_MODE_NATURAL)
    {
        if(is_array($columns) && count($columns) > 1) {
            return parent::getMatchQuery($columns, $expression, $mode);
        }

        $columns = is_array($columns) ? array_pop($columns):$columns;
        $expression = str_replace('*', '', $expression);
        $expression = str_replace('%', '%%', $expression);
        $expression = $this->connection->quote('%'.$expression.'%');

        $condition = $columns.' LIKE '.$expression;
        return $condition;
    }
}
