<?php

namespace DevHongZui\AuctionProducts\Model\AuctionBidder;

use DevHongZui\AuctionProducts\Model\Auction;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionBidder\CollectionFactory as AuctionBidderCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected array $loadedData;

    protected RequestInterface $request;

    protected Auction $auction;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param AuctionBidderCollectionFactory $auctionCollectionFactory
     * @param RequestInterface $request
     * @param Auction $auction
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        AuctionBidderCollectionFactory $auctionCollectionFactory,
        RequestInterface $request,
        Auction $auction,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->requestFieldName = 'parent_id';
        $this->collection = $auctionCollectionFactory->create();
        $this->request = $request;
        $this->auction = $auction;
        $this->loadedData = [];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $this->collection->addFieldToFilter(
            'auction_id',
            $this->request->getParam($this->getRequestFieldName())
        );

        return [
            'items' => $this->collection->getData(),
            'totalRecords' => $this->collection->count()
        ];
    }
}
