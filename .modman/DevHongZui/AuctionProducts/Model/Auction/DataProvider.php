<?php

namespace DevHongZui\AuctionProducts\Model\Auction;

use DevHongZui\AuctionProducts\Model\Auction;
use DevHongZui\AuctionProducts\Model\ResourceModel\Auction\CollectionFactory as AuctionCollectionFactory;
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
     * @param AuctionCollectionFactory $auctionCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Auction $auction
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        AuctionCollectionFactory $auctionCollectionFactory,
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
        $id = array_first($this->getAllIds());

        if ($id)
            if ($data_persistor = $this->getDataPersistor())
                $this->loadedData[$id] = $data_persistor;
            else {
                $this->loadedData[$id]['general'] = $this->getSearchResult()->getFirstItem()->getData();

                if (is_string($this->loadedData[$id]['general']['product_ids']))
                    $this->loadedData[$id]['general']['product_ids'] = $this->getProductSkus(
                        $this->loadedData[$id]['general']['product_ids']
                    );

                if (is_numeric($this->loadedData[$id]['general']['start_price']))
                    $this->loadedData[$id]['general']['start_price'] = number_format(
                        $this->loadedData[$id]['general']['start_price'], 2
                    );

                if (is_numeric($this->loadedData[$id]['general']['step_price']))
                    $this->loadedData[$id]['general']['step_price'] = number_format(
                        $this->loadedData[$id]['general']['step_price'], 2
                    );

                if (is_numeric($this->loadedData[$id]['general']['reserve_price']))
                    $this->loadedData[$id]['general']['reserve_price'] = number_format(
                        $this->loadedData[$id]['general']['reserve_price'], 2
                    );

                if (is_numeric($this->loadedData[$id]['general']['limit_price']))
                    $this->loadedData[$id]['general']['limit_price'] = number_format(
                        $this->loadedData[$id]['general']['limit_price'], 2
                    );
            }

        return $this->loadedData;
    }

    /**
     * @return array
     */
    protected function getDataPersistor(): array
    {
        $data = $this->dataPersistor->get('auction') ?? array();

        if ($data)
            $this->dataPersistor->clear('auction');

        return $data;
    }

    /**
     * @param string $product_ids
     * @return string
     */
    protected function getProductSkus(string $product_ids): string
    {
        return $this->auction->getProductSkus($product_ids);
    }
}
