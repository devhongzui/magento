<?php

namespace DevHongZui\AuctionProducts\Ui\Component\Listing\Column\AuctionBidder;

use DevHongZui\AuctionProducts\Model\AuctionBidder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    protected AuctionBidder $auctionBidder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AuctionBidder $auctionBidder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        AuctionBidder      $auctionBidder,
        array              $components = [],
        array              $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->auctionBidder = $auctionBidder;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']))
            foreach ($dataSource['data']['items'] as &$item)
                $item[$this->getName()] = $this->auctionBidder->getStatusLabel(
                    $item[$this->getName()]
                );

        return $dataSource;
    }
}
