<?php

namespace DevHongZui\AuctionProducts\Model\ResourceModel;

use DevHongZui\AuctionProducts\Model\AuctionProductFactory;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionProduct\CollectionFactory as AuctionProductCollectionFactory;
use Exception;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Auction extends AbstractDb
{
    const TABLE_NAME = 'devhongzui_auctions';

    const TABLE_ID = 'entity_id';

    protected ProductCollectionFactory $productCollectionFactory;

    protected AuctionProductFactory $auctionProductFactory;

    protected AuctionProductCollectionFactory $auctionProductCollectionFactory;

    /**
     * @param Context $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param AuctionProductFactory $auctionProductFactory
     * @param AuctionProductCollectionFactory $auctionProductCollectionFactory
     * @param null $connectionName
     */
    public function __construct(
        Context                         $context,
        ProductCollectionFactory        $productCollectionFactory,
        AuctionProductFactory           $auctionProductFactory,
        AuctionProductCollectionFactory $auctionProductCollectionFactory,
                                        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);

        $this->productCollectionFactory = $productCollectionFactory;
        $this->auctionProductFactory = $auctionProductFactory;
        $this->auctionProductCollectionFactory = $auctionProductCollectionFactory;
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::TABLE_ID);
    }

    /**
     * @param AbstractModel $object
     * @return Auction
     */
    protected function _beforeSave(AbstractModel $object): Auction
    {
        $product_collection = $this->productCollectionFactory->create();

        $new_product = explode(',', $object->getData('new_product_ids'));

        foreach ($new_product as &$value)
            $value = trim($value);

        $new_product_ids = $product_collection->addFieldToFilter('sku', $new_product)->getAllIds();
        $product_ids = implode(',', $new_product_ids);

        $object
            ->setData('new_product_ids', $new_product_ids)
            ->setData('product_ids', $product_ids);

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return Auction
     * @throws Exception
     */
    protected function _afterSave(AbstractModel $object): Auction
    {
        $auction_id = $object->getId();
        $auction_status = $object->getData('status');

        $auction_product_collection = $this->auctionProductCollectionFactory->create();

        $old_product_ids = $auction_product_collection->getProductIds($auction_id);
        $new_product_ids = $object->getData('new_product_ids');

        $insert = array_diff($new_product_ids, $old_product_ids);
        $delete = array_diff($old_product_ids, $new_product_ids);

        if ($insert) {
            $this->insertProductListInAuctionProductTable($insert, $auction_id, $auction_status);
//            $this->changeEAVProduct($insert, $auction_id);
        }

        if ($delete) {
            $this->deleteProductListInAuctionProductTable($delete, $auction_id);
//            $this->changeEAVProduct($delete);
        }


        return parent::_afterSave($object);
    }

    /**
     * @param array $product_ids
     * @param int $auction_id
     * @param int $auction_status
     * @return void
     * @throws Exception
     */
    protected function insertProductListInAuctionProductTable(
        array $product_ids,
        int   $auction_id,
        int   $auction_status
    ): void
    {
        $auction_product_model = $this->auctionProductFactory->create();

        foreach ($product_ids as $product_id)
            $auction_product_model
                ->setData([
                    'product_id' => $product_id,
                    'auction_id' => $auction_id,
                    'status' => $auction_status
                ])
                ->save();
    }

    /**
     * @param array $product_ids
     * @param int $auction_id
     * @return void
     */
    protected function deleteProductListInAuctionProductTable(array $product_ids, int $auction_id): void
    {
        $auction_product_collection = $this->auctionProductCollectionFactory->create();

        $auction_product_collection
            ->addFieldToFilter('product_id', $product_ids)
            ->addFieldToFilter('auction_id', $auction_id);

        foreach ($auction_product_collection as $item)
            $item->delete();
    }

    /**
     * @param array $product_ids
     * @param int|null $auction_id
     * @return void
     */
    protected function changeEAVProduct(array $product_ids, int $auction_id = null): void
    {
        $product_collection = $this->productCollectionFactory->create();

        $product_collection->addIdFilter('id', $product_ids);

        foreach ($product_collection as $item)
            $item->setData('auction_id', $auction_id)->save();
    }
}
