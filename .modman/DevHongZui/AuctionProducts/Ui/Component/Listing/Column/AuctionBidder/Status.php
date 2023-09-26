<?php

namespace DevHongZui\AuctionProducts\Ui\Component\Listing\Column\AuctionBidder;

use DevHongZui\AuctionProducts\Model\AuctionBidder\Status as AuctionBidderStatus;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    protected AuctionBidderStatus $auctionBidderStatus;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AuctionBidderStatus $auctionBidderStatus
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface    $context,
        UiComponentFactory  $uiComponentFactory,
        AuctionBidderStatus $auctionBidderStatus,
        array               $components = [],
        array               $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->auctionBidderStatus = $auctionBidderStatus;
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
                $item[$name] = $this->auctionBidderStatus->getOptionText($item[$name]);
            }

        return $dataSource;
    }
}
