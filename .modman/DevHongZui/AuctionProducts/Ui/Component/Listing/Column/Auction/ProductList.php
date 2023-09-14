<?php

namespace DevHongZui\AuctionProducts\Ui\Component\Listing\Column\Auction;

use DevHongZui\AuctionProducts\Model\Auction;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ProductList extends Column
{
    protected Auction $auction;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Auction $auction
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        Auction            $auction,
        array              $components = [],
        array              $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->auction = $auction;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']))
            foreach ($dataSource['data']['items'] as &$item)
                $item[$this->getName()] = $this->auction->getProductSkus(
                    $item[$this->getName()]
                );

        return $dataSource;
    }
}
