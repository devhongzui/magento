<?php

namespace DevHongZui\AuctionProducts\Ui\Component\Listing\Column\Auction;

use DevHongZui\AuctionProducts\Model\Auction\Status as AuctionStatus;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    protected AuctionStatus $auctionStatus;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AuctionStatus $auctionStatus
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        AuctionStatus      $auctionStatus,
        array              $components = [],
        array              $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->auctionStatus = $auctionStatus;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']))
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getName();
                $item[$name] = $this->auctionStatus->getOptionText($item[$name]);
            }

        return $dataSource;
    }
}
