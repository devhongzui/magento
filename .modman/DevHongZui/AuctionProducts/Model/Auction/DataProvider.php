<?php

namespace DevHongZui\AuctionProducts\Model\Auction;

use DevHongZui\AuctionProducts\Model\Auction;
use DevHongZui\AuctionProducts\Model\ResourceModel\Auction\CollectionFactory as AuctionCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected array $loadedData;

    protected DataPersistorInterface $dataPersistor;

    protected Auction $auction;

    protected RequestInterface $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param AuctionCollectionFactory $auctionCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Auction $auction
     * @param RequestInterface $request
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
        RequestInterface $request,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->requestFieldName = 'id';
        $this->collection = $auctionCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->auction = $auction;
        $this->request = $request;
        $this->loadedData = [];
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        $meta = parent::getMeta();

        if (
            ($id = $this->request->getParam('id')) &&
            is_string($this->auction->haveSave($id))
        ) {
            $meta['general']['children']['product_ids']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['start_price']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['step_price']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['reserve_price']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['limit_price']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['start_at']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['stop_at']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['days']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['status']['arguments']['data']['config']['disabled'] = true;
            $meta['general']['children']['disabled']['arguments']['data']['config']['disabled'] = true;
        }

        return $meta;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $id = array_first($this->getAllIds());

        if ($data_persistor = $this->getDataPersistor())
            $this->loadedData[$id] = $data_persistor;
        else if ($id) {
            $this->loadedData[$id]['general'] = $this->getSearchResult()->getFirstItem()->getData();

            if (is_string($this->loadedData[$id]['general']['product_ids']))
                $this->loadedData[$id]['general']['product_ids'] = $this->getProductSkus(
                    $this->loadedData[$id]['general']['product_ids']
                );

            if (is_numeric($this->loadedData[$id]['general']['start_price']))
                $this->loadedData[$id]['general']['start_price'] = round(
                    $this->loadedData[$id]['general']['start_price']
                );

            if (is_numeric($this->loadedData[$id]['general']['step_price']))
                $this->loadedData[$id]['general']['step_price'] = round(
                    $this->loadedData[$id]['general']['step_price']
                );

            if (is_numeric($this->loadedData[$id]['general']['reserve_price']))
                $this->loadedData[$id]['general']['reserve_price'] = round(
                    $this->loadedData[$id]['general']['reserve_price']
                );

            if (is_numeric($this->loadedData[$id]['general']['limit_price']))
                $this->loadedData[$id]['general']['limit_price'] = round(
                    $this->loadedData[$id]['general']['limit_price']
                );

            $this->loadedData[$id]['general']['disabled'] = match ((int)$this->loadedData[$id]['general']['status']) {
                Auction::STATUS_NOT_START,
                Auction::STATUS_PROCESSING,
                Auction::STATUS_FINISHED,
                Auction::STATUS_ENDED => '0',

                default => '1',
            };

            $this->loadedData[$id]['general']['status'] = $this->auction->getStatusLabel(
                $this->loadedData[$id]['general']['status']
            );
        } else {
            $this->loadedData[null]['general']['status'] = __('The status will be displayed after saving');
            $this->loadedData[null]['general']['disabled'] = '0';
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
