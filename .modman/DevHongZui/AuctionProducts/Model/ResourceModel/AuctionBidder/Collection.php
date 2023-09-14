<?php

namespace DevHongZui\AuctionProducts\Model\ResourceModel\AuctionBidder;

use DevHongZui\AuctionProducts\Model\AuctionBidder as BidderModel;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionBidder as BidderResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = BidderResourceModel::TABLE_ID;

    protected $_eventPrefix = BidderModel::CACHE_TAG . '_collection';

    protected $_eventObject = BidderModel::CACHE_TAG . '_collection';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->_init(BidderModel::class, BidderResourceModel::class);
    }
}
