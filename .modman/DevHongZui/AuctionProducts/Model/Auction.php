<?php

namespace DevHongZui\AuctionProducts\Model;

use Magento\Catalog\Model\Product\Interceptor;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

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

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProductCollectionFactory $productCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                  $context,
        Registry                 $registry,
        ProductCollectionFactory $productCollectionFactory,
        AbstractResource         $resource = null,
        AbstractDb               $resourceCollection = null,
        array                    $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->productCollectionFactory = $productCollectionFactory;
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

        $product_collection
            ->addIdFilter($handle);

        $data = [];

        /** @var Interceptor $item */
        foreach ($product_collection as $item) {
            $data[] = $item->getSku();
        }

        return implode(', ', $data);
    }

    /**
     * @param int|null $auction_id
     * @return bool
     */
    public function haveDelete(int $auction_id = null): bool
    {
        if (is_null($auction_id))
            $auction_id = $this->getId();

        return true;
    }

    /**
     * @param int|null $auction_id
     * @return bool
     */
    public function haveSave(int $auction_id = null): bool
    {
        if (is_null($auction_id))
            $auction_id = $this->getId();

        return true;
    }
}
