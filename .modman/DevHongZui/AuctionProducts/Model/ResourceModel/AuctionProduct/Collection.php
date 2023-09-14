<?php

namespace DevHongZui\AuctionProducts\Model\ResourceModel\AuctionProduct;

use DevHongZui\AuctionProducts\Model\AuctionProduct as ProductModel;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionProduct as ProductResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = ProductResourceModel::TABLE_ID;

    protected $_eventPrefix = ProductModel::CACHE_TAG . '_collection';

    protected $_eventObject = ProductModel::CACHE_TAG . '_collection';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->_init(ProductModel::class, ProductResourceModel::class);
    }
}
