<?php

namespace DevHongZui\AuctionProducts\Model;

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

    const STATUS_NOT_START = 0;

    const STATUS_PROCESSING = 1;

    const STATUS_FINISHED = 2;

    const STATUS_CLOSED = 3;

    const STATUS_DISABLED = 4;

    const STATUS_ENDED = 5;

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = self::CACHE_TAG;

    protected ProductCollectionFactory $productCollectionFactory;

    protected TimezoneInterface $timezone;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProductCollectionFactory $productCollectionFactory
     * @param TimezoneInterface $timezone
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                  $context,
        Registry                 $registry,
        ProductCollectionFactory $productCollectionFactory,
        TimezoneInterface        $timezone,
        AbstractResource         $resource = null,
        AbstractDb               $resourceCollection = null,
        array                    $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->productCollectionFactory = $productCollectionFactory;
        $this->timezone = $timezone;
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
        $step_price = $this->getData('step_price');
        $reserve_price = $this->getData('reserve_price');
        $limit_price = $this->getData('limit_price');

        if ($start_price < $step_price)
            throw new Exception(__(
                'Start Price < Step Price'
            ));

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
     * @return array
     */
    public function getAllStatus(): array
    {
        return [
            Auction::STATUS_NOT_START => __('Not Start'),
            Auction::STATUS_PROCESSING => __('Processing'),
            Auction::STATUS_FINISHED => __('Finished'),
            Auction::STATUS_CLOSED => __('Closed'),
            Auction::STATUS_DISABLED => __('Disabled'),
            Auction::STATUS_ENDED => __('End')
        ];
    }

    /**
     * @param int|null $status
     * @return string
     */
    public function getStatusLabel(int $status = null): string
    {
        $labels = $this->getAllStatus();

        return is_null($status)
            ? $labels[$this->getData('status')]
            : $labels[$status];
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
            $status = $this->getData('status');
        } else {
            $auction = $this->load($auction_id);
            $stop_at_raw = $auction->getData('stop_at');
            $status = $auction->getData('status');
        }

        $current_time_raw = $this->getCurrentTimeUTC();
        $current_time = strtotime($current_time_raw);
        $stop_at = strtotime($stop_at_raw);

        if ($status != self::STATUS_ENDED)
            return __('Auctions with a status of Ended can only be deleted');

        if ($current_time < $stop_at)
            return __('Auctions can only be deleted when the timer expires');

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
