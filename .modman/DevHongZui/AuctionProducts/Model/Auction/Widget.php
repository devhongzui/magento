<?php

namespace DevHongZui\AuctionProducts\Model\Auction;

use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionProduct\Collection;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionProduct\CollectionFactory as AuctionProductCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Data\OptionSourceInterface;

class Widget implements OptionSourceInterface
{
    protected AuctionProductCollectionFactory $auctionProductCollectionFactory;

    protected Attribute $attribute;

    /**
     * @param AuctionProductCollectionFactory $auctionProductCollectionFactory
     * @param Attribute $attribute
     */
    public function __construct(
        AuctionProductCollectionFactory $auctionProductCollectionFactory,
        Attribute                       $attribute
    )
    {
        $this->auctionProductCollectionFactory = $auctionProductCollectionFactory;
        $this->attribute = $attribute;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $auction_product_collection = $this->getItems();

        $data = [];

        foreach ($auction_product_collection as $item) {
            $data[] = [
                'label' => __(
                    'Product: %1 (Auction ID: %2 - Product ID: %3)',
                    $item->getData('product_name'),
                    $item->getData('auction_id'),
                    $item->getData('product_id')
                ),
                'value' => $item->getData('entity_id')
            ];
        }

        return $data;
    }

    /**
     * @return Collection
     */
    protected function getItems(): Collection
    {
        $auction_product_collection = $this->auctionProductCollectionFactory->create();

        $name_is_EAV = $this->attribute->getIdByCode('catalog_product', 'name');

        $auction_product_collection
            ->addFieldToSelect(['auction_id', 'product_id'])
            ->getSelect()
            ->joinLeft(
                ['e' => 'catalog_product_entity_varchar'],
                "main_table.product_id = e.entity_id AND e.attribute_id = $name_is_EAV",
                ['product_name' => 'e.value']
            );

        return $auction_product_collection;
    }
}
