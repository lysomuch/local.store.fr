<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Review\Model\ResourceModel\Rating;

class Option extends \Magento\Review\Model\ResourceModel\Rating\Option
{
    /**
     * Add vote
     *
     * @param \Magento\Review\Model\Rating\Option $option
     * @return $this
     */
    public function addVote($option)
    {
        $connection = $this->getConnection();
        $optionData = $this->loadDataById($option->getId());
        $data = [
            'option_id' => $option->getId(),
            'review_id' => $option->getReviewId(),
            'percent' => $optionData['value'] / 5 * 100,
            'value' => $optionData['value'],
        ];

        if (!$option->getDoUpdate()) {
            $data['remote_ip'] = $this->_remoteAddress->getRemoteAddress();
            $data['remote_ip_long'] = $this->_remoteAddress->getRemoteAddress(true);
            $data['customer_id'] = null;
            $data['entity_pk_value'] = $option->getEntityPkValue();
            $data['rating_id'] = $option->getRatingId();
        }

        $connection->beginTransaction();
        try {
            if ($option->getDoUpdate()) {
                $condition = ['vote_id = ?' => $option->getVoteId(), 'review_id = ?' => $option->getReviewId()];
                $connection->update($this->_ratingVoteTable, $data, $condition);
                $this->aggregate($option);
            } else {
                $connection->insert($this->_ratingVoteTable, $data);
                $option->setVoteId($connection->lastInsertId($this->_ratingVoteTable));
                $this->aggregate($option);
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new \Exception($e->getMessage());
        }
        return $this;
    }
}
