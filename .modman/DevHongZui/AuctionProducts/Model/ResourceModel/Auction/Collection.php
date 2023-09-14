<?php

namespace DevHongZui\AuctionProducts\Model\ResourceModel\Auction;

use DevHongZui\AuctionProducts\Model\Auction as AuctionModel;
use DevHongZui\AuctionProducts\Model\ResourceModel\Auction as AuctionResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = AuctionResourceModel::TABLE_ID;

    protected $_eventPrefix = AuctionModel::CACHE_TAG . '_collection';

    protected $_eventObject = AuctionModel::CACHE_TAG . '_collection';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->_init(AuctionModel::class, AuctionResourceModel::class);
    }
}
