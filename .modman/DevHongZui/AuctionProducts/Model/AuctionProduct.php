<?php

namespace DevHongZui\AuctionProducts\Model;

use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionBidder\CollectionFactory as AuctionBidderCollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class AuctionProduct extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'devhongzui_auctionProducts_auctionProduct';

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = self::CACHE_TAG;

    protected AuctionBidderCollectionFactory $auctionBidderCollectionFactory;

    protected AuctionFactory $auctionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param AuctionBidderCollectionFactory $auctionBidderCollectionFactory
     * @param AuctionFactory $auctionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                        $context,
        Registry                       $registry,
        AuctionBidderCollectionFactory $auctionBidderCollectionFactory,
        AuctionFactory                 $auctionFactory,
        AbstractResource               $resource = null,
        AbstractDb                     $resourceCollection = null,
        array                          $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->auctionBidderCollectionFactory = $auctionBidderCollectionFactory;
        $this->auctionFactory = $auctionFactory;
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->_init(ResourceModel\AuctionProduct::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [$this->_cacheTag . '_' . $this->getId()];
    }

    /**
     * @param int|null $auction_product_id
     * @return float
     */
    public function getHighestPrice(int $auction_product_id = null): float
    {
        if (is_null($auction_product_id)) {
            $auction_id = $this->getData('auction_id');
        } else {
            $auction_product_model = $this->load($auction_product_id);
            $auction_id = $auction_product_model->getData('auction_id');
        }

        $auction_bid = $this->getHighestPriceEntity($auction_product_id);

        if ($bid_price = $auction_bid->getData('bid_price'))
            return $bid_price;
        else {
            $auction_model = $this->auctionFactory->create();

            $auction_model->load($auction_id);

            return $auction_model->getData('start_price');
        }
    }

    /**
     * @param int|null $auction_product_id
     * @return DataObject
     */
    public function getHighestPriceEntity(int $auction_product_id = null): DataObject
    {
        if (is_null($auction_product_id)) {
            $auction_id = $this->getData('auction_id');
            $product_id = $this->getData('product_id');
        } else {
            $auction_product_model = $this->load($auction_product_id);
            $auction_id = $auction_product_model->getData('auction_id');
            $product_id = $auction_product_model->getData('product_id');
        }

        $auction_bidder_collection = $this->auctionBidderCollectionFactory->create();

        $auction_bidder_collection
            ->addFieldToFilter('auction_id', $auction_id)
            ->addFieldToFilter('product_id', $product_id)
            ->setOrder('created_at');

        return $auction_bidder_collection->getFirstItem();
    }
}
