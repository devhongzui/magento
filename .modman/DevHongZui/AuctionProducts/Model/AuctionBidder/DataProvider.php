<?php

namespace DevHongZui\AuctionProducts\Model\AuctionBidder;

use DevHongZui\AuctionProducts\Model\Auction;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionBidder\CollectionFactory as AuctionBidderCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected array $loadedData;

    protected DataPersistorInterface $dataPersistor;

    protected Auction $auction;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param AuctionBidderCollectionFactory $auctionCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Auction $auction
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        AuctionBidderCollectionFactory $auctionCollectionFactory,
        DataPersistorInterface $dataPersistor,
        Auction $auction,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->requestFieldName = 'id';
        $this->collection = $auctionCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->auction = $auction;
        $this->loadedData = [];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $auction_id = $this->dataPersistor->get('auction_id');

        $this->collection->addFieldToFilter('auction_id', $auction_id);

        return [
            'items' => $this->collection->getData(),
            'totalRecords' => $this->collection->count()
        ];
    }
}
