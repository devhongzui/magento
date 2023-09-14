<?php

namespace DevHongZui\AuctionProducts\Ui\Component\Listing\Column\AuctionProduct;

use DevHongZui\AuctionProducts\Model\AuctionProduct;
use Magento\Framework\Pricing\Helper\Data as PricingHelperData;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class HighestPrice extends Column
{
    protected AuctionProduct $auctionProduct;

    protected PricingHelperData $pricingHelperData;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AuctionProduct $auctionProduct
     * @param PricingHelperData $pricingHelperData
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        AuctionProduct     $auctionProduct,
        PricingHelperData  $pricingHelperData,
        array              $components = [],
        array              $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->auctionProduct = $auctionProduct;
        $this->pricingHelperData = $pricingHelperData;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']))
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getName() . '_raw'] = $item[$this->getName()];

                $item[$this->getName()] = $this->getHighestPrice(
                    $item[$this->getName()]
                );
            }

        return $dataSource;
    }

    /**
     * @param int $auction_product_id
     * @return string
     */
    protected function getHighestPrice(int $auction_product_id): string
    {
        $highest_price = $this->auctionProduct->getHighestPrice($auction_product_id);

        return $this->pricingHelperData->currency($highest_price, includeContainer: false);
    }
}
