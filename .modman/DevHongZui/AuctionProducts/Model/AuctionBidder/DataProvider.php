<?php

namespace DevHongZui\AuctionProducts\Model\AuctionBidder;

use DevHongZui\AuctionProducts\Model\Auction;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionBidder\CollectionFactory as AuctionBidderCollectionFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected array $loadedData;

    protected RequestInterface $request;

    protected Auction $auction;

    protected Attribute $attribute;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param AuctionBidderCollectionFactory $auctionCollectionFactory
     * @param RequestInterface $request
     * @param Auction $auction
     * @param Attribute $attribute
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        AuctionBidderCollectionFactory $auctionCollectionFactory,
        RequestInterface $request,
        Auction $auction,
        Attribute $attribute,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->requestFieldName = 'parent_id';
        $this->collection = $auctionCollectionFactory->create();
        $this->request = $request;
        $this->auction = $auction;
        $this->attribute = $attribute;
        $this->loadedData = [];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $attribute_id = $this->attribute->getIdByCode(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            ProductAttributeInterface::CODE_NAME
        );

        $this->collection
            ->addFieldToFilter(
                'auction_id',
                $this->request->getParam($this->getRequestFieldName())
            )
            ->getSelect()
            ->joinLeft(
                ['e' => 'customer_grid_flat'],
                'main_table.customer_id = e.entity_id',
                ['customer_name' => 'e.name']
            )
            ->joinLeft(
                ['f' => 'catalog_product_entity_varchar'],
                sprintf(
                    'main_table.product_id = f.entity_id AND f.attribute_id = %d',
                    $attribute_id
                ),
                ['product_name' => 'f.value']
            );

        return [
            'items' => $this->collection->getData(),
            'totalRecords' => $this->collection->count()
        ];
    }
}
