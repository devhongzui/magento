<?php

namespace DevHongZui\AuctionProducts\Ui\Component\Listing\Column;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ProductName extends Column
{
    protected ProductRepositoryInterface $productRepositoryInterface;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface           $context,
        UiComponentFactory         $uiComponentFactory,
        ProductRepositoryInterface $productRepositoryInterface,
        array                      $components = [],
        array                      $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']))
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getName();
                $item[$name] = $this->getProductName($item[$name]);
            }

        return $dataSource;
    }

    /**
     * @param int $product_id
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getProductName(int $product_id): string
    {
        return $this->productRepositoryInterface->getById($product_id)->getName();
    }
}
