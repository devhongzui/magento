<?php

namespace DevHongZui\AuctionProducts\Model\Auction;

use DevHongZui\AuctionProducts\Model\ResourceModel\Auction\CollectionFactory as AuctionCollectionFactory;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Filter extends AbstractFilter
{
    protected UrlInterface $url;

    protected AuctionCollectionFactory $auctionCollectionFactory;

    /**
     * @param ItemFactory $filterItemFactory
     * @param StoreManagerInterface $storeManager
     * @param Layer $layer
     * @param DataBuilder $itemDataBuilder
     * @param UrlInterface $url
     * @param AuctionCollectionFactory $auctionCollectionFactory
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        ItemFactory              $filterItemFactory,
        StoreManagerInterface    $storeManager,
        Layer                    $layer,
        DataBuilder              $itemDataBuilder,
        UrlInterface             $url,
        AuctionCollectionFactory $auctionCollectionFactory,
        array                    $data = []
    )
    {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);

        $this->url = $url;
        $this->auctionCollectionFactory = $auctionCollectionFactory;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function apply(RequestInterface $request): Filter
    {
        if ($this->canApply()) {
            $auction_ids = $this->getAuctionIds();

            $this->getLayer()->getProductCollection()->addFieldToFilter(
                'auction_id',
                empty($auction_ids) ? array('null') : $auction_ids
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getAuctionIds(): array
    {
        $auction_collection = $this->auctionCollectionFactory->create();

        $auction_collection
            ->addFieldToFilter('status', ['neq' => Status::STATUS_DISABLED])
            ->addFieldToSelect('entity_id');

        return $auction_collection->getAllIds();
    }

    /**
     * @return bool
     */
    public function canApply(): bool
    {
        $result = strpos($this->url->getCurrentUrl(), 'auction/product/listing');

        return is_int($result);
    }
}
