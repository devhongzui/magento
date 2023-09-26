<?php

namespace DevHongZui\AuctionProducts\Model;

use DevHongZui\AuctionProducts\Model\Auction\Status;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionProduct\CollectionFactory as AuctionProductCollectionFactory;
use Exception;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Auction extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'devhongzui_auctionProducts_auction';

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = self::CACHE_TAG;

    protected ProductCollectionFactory $productCollectionFactory;

    protected AuctionProductCollectionFactory $auctionProductCollectionFactory;

    protected TimezoneInterface $timezone;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProductCollectionFactory $productCollectionFactory
     * @param TimezoneInterface $timezone
     * @param AuctionProductCollectionFactory $auctionProductCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                         $context,
        Registry                        $registry,
        ProductCollectionFactory        $productCollectionFactory,
        TimezoneInterface               $timezone,
        AuctionProductCollectionFactory $auctionProductCollectionFactory,
        AbstractResource                $resource = null,
        AbstractDb                      $resourceCollection = null,
        array                           $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->productCollectionFactory = $productCollectionFactory;
        $this->timezone = $timezone;
        $this->auctionProductCollectionFactory = $auctionProductCollectionFactory;
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->_init(ResourceModel\Auction::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [$this->_cacheTag . '_' . $this->getId()];
    }

    /**
     * @return Auction
     * @throws Exception
     * @throws Exception
     */
    public function validateBeforeSave(): Auction
    {
        $message = $this->haveSave();

        if (is_string($message))
            throw new Exception($message);

        $start_price = $this->getData('start_price');
        $reserve_price = $this->getData('reserve_price');
        $limit_price = $this->getData('limit_price');

        if ($limit_price < $start_price)
            throw new Exception(__(
                'Limit Price < Start Price'
            ));

        if ($reserve_price < $start_price)
            throw new Exception(__(
                'Reserve Price < Start Price'
            ));

        if ($reserve_price < $limit_price)
            throw new Exception(__(
                'Reserve Price < Limit Price'
            ));

        $start_at_raw = $this->getData('start_at');
        $stop_at_raw = $this->getData('stop_at');

        $start_at = strtotime($start_at_raw);
        $stop_at = strtotime($stop_at_raw);

        if ($stop_at < $start_at)
            throw new Exception(__(
                'Stop At < Start At'
            ));

        $product_skus = $this->getData('product_ids');
        $new_product_skus = explode(',', $product_skus);
        $new_product_ids = $this->getResource()->getNewProductIds($product_skus);

        if (count($new_product_skus) != count($new_product_ids))
            throw new Exception(__(
                'The product in the list does not exist'
            ));

        $auction_ids = $this->getCollection()
            ->addFieldToFilter('entity_id', ['neq' => $this->getId()])
            ->addFieldToFilter('status', ['neq' => Status::STATUS_ENDED])
            ->addFieldToFilter('stop_at', ['gt' => $this->getCurrentTimeUTC()])
            ->getAllIds();

        if ($auction_ids) {
            $auction_product_collection = $this->auctionProductCollectionFactory->create();
            $auction_product_collection
                ->addFieldToFilter('main_table.product_id', $new_product_ids)
                ->addFieldToFilter('auction_id', $auction_ids);

            if ($auction_product_collection->getFirstItem()->getData())
                throw new Exception(__(
                    'The product in the list already exists in 1 Auction that has not yet ended'
                ));
        }

        return parent::validateBeforeSave();
    }

    /**
     * @return string
     */
    protected function getCurrentTimeUTC(): string
    {
        return $this->timezone->date(useTimezone: false)->format('Y-m-d H:i:s');
    }

    /**
     * @param string|null $product_ids
     * @return string
     */
    public function getProductSkus(string $product_ids = null): string
    {
        $handle = explode(',', is_null($product_ids)
            ? $this->getData('product_ids')
            : $product_ids);

        $product_collection = $this->productCollectionFactory->create();

        $product_collection->addIdFilter($handle);

        return implode(
            ', ',
            $product_collection->getColumnValues('sku')
        );
    }

    /**
     * @param int|null $auction_id
     * @return bool|string
     */
    public function haveDelete(int $auction_id = null): bool|string
    {
        if (is_null($auction_id)) {
            $stop_at_raw = $this->getData('stop_at');
        } else {
            $auction = $this->load($auction_id);
            $stop_at_raw = $auction->getData('stop_at');
        }

        if ($stop_at_raw) {
            $current_time_raw = $this->getCurrentTimeUTC();
            $current_time = strtotime($current_time_raw);
            $stop_at = strtotime($stop_at_raw);

            if ($current_time < $stop_at)
                return __('Auctions can only be deleted when the timer expires');
        }

        return true;
    }

    /**
     * @param int|null $auction_id
     * @return bool|string
     */
    public function haveSave(int $auction_id = null): bool|string
    {
        if (is_null($auction_id)) {
            $start_at_raw = $this->getData('start_at');
        } else {
            $auction = $this->load($auction_id);
            $start_at_raw = $auction->getData('start_at');
        }

        if ($start_at_raw) {
            $current_time_raw = $this->getCurrentTimeUTC();
            $current_time = strtotime($current_time_raw);
            $start_at = strtotime($start_at_raw);

            if ($start_at < $current_time)
                return __('Auctions can only be edited when they have not yet started');
        }

        return true;
    }
}
