<?php

namespace DevHongZui\AuctionProducts\Ui\Component\Listing\Column\AuctionProduct;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    protected UrlInterface $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface       $urlBuilder,
        array              $components = [],
        array              $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']))
            foreach ($dataSource['data']['items'] as & $item)
                if (isset($item['entity_id']))
                    $item[$this->getData('name')] = [
                        'view' => $this->getViewButton($item['entity_id']),
                    ];

        return $dataSource;
    }

    /**
     * @param int $id
     * @return array
     */
    private function getViewButton(int $id): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                'auction/bid/index',
                ['id' => $id]
            ),
            'label' => __('View')
        ];
    }
}
