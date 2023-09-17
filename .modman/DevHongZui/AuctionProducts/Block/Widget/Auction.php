<?php

namespace DevHongZui\AuctionProducts\Block\Widget;

use DevHongZui\AuctionProducts\Model\Auction as AuctionModel;
use DevHongZui\AuctionProducts\Model\AuctionBidder;
use DevHongZui\AuctionProducts\Model\AuctionProduct;
use DevHongZui\AuctionProducts\Model\ResourceModel\Auction as AuctionResourceModel;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionProduct\Collection;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionProduct\CollectionFactory as AuctionProductCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\Url;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PricingHelperData;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Widget\Block\BlockInterface;

class Auction extends AbstractProduct implements BlockInterface, IdentityInterface
{
    protected $_template = "widget/auction.phtml";

    protected FormKey $formKey;

    protected AuctionProductCollectionFactory $auctionProductCollectionFactory;

    protected AuctionProduct $auctionProduct;

    protected SessionFactory $sessionFactory;

    protected TimezoneInterface $timezone;

    protected Url $url;

    protected ProductRepository $productRepository;

    protected PricingHelperData $pricingHelperData;

    /**
     * @param Context $context
     * @param FormKey $formKey
     * @param AuctionProductCollectionFactory $auctionProductCollectionFactory
     * @param AuctionProduct $auctionProduct
     * @param SessionFactory $sessionFactory
     * @param TimezoneInterface $timezone
     * @param Url $url
     * @param ProductRepository $productRepository
     * @param PricingHelperData $pricingHelperData
     * @param array $data
     */
    public function __construct(
        Context                         $context,
        FormKey                         $formKey,
        AuctionProductCollectionFactory $auctionProductCollectionFactory,
        AuctionProduct                  $auctionProduct,
        SessionFactory                  $sessionFactory,
        TimezoneInterface               $timezone,
        Url                             $url,
        ProductRepository               $productRepository,
        PricingHelperData               $pricingHelperData,
        array                           $data = []
    )
    {
        parent::__construct($context, $data);

        $this->formKey = $formKey;
        $this->auctionProductCollectionFactory = $auctionProductCollectionFactory;
        $this->auctionProduct = $auctionProduct;
        $this->sessionFactory = $sessionFactory;
        $this->timezone = $timezone;
        $this->url = $url;
        $this->productRepository = $productRepository;
        $this->pricingHelperData = $pricingHelperData;
    }

    /**
     * @return string|null
     */
    public function getFormKey(): ?string
    {
        try {
            return $this->formKey->getFormKey();
        } catch (LocalizedException) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        $session = $this->sessionFactory->create();

        return $session->isLoggedIn();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData('title') ?? __('Auction Products');
    }

    /**
     * @return string
     */
    public function getTitleImage(): string
    {
        return $this->getData('title_image') ?? '';
    }

    /**
     * @return string
     */
    public function getLoginUrl(): string
    {
        return $this->url->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getViewAllButton(): string
    {
        return $this->getData('view_all_button') ?? __('View All');
    }

    /**
     * @param $price
     * @return string
     */
    public function formatPrice($price): string
    {
        return $this->pricingHelperData->currency($price, includeContainer: false);
    }

    /**
     * @param string $time
     * @return string
     */
    public function switchTimeZone(string $time): string
    {
        return $this->timezone->date($time)->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getCurrentTime(): string
    {
        return $this->timezone->date()->format('Y-m-d H:i:s');
    }

    /**
     * @param int $product_id
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getProductById(int $product_id): ProductInterface
    {
        return $this->productRepository->getById($product_id);
    }

    /**
     * @return Collection
     */
    public function getProductList(): Collection
    {
        $auction_product_collection = $this->auctionProductCollectionFactory->create();

        $auction_product_ids = explode(',', $this->getData('product_list'));

        $auction_product_collection
            ->addFieldToFilter('main_table.entity_id', $auction_product_ids)
            ->setPageSize($this->getData('product_limit') ?? 2)
            ->getSelect()
            ->joinLeft(
                ['e' => AuctionResourceModel::TABLE_NAME],
                'main_table.auction_id = e.entity_id',
                ['e.step_price', 'e.start_at', 'e.stop_at']
            );

        return $auction_product_collection;
    }

    /**
     * @param int $auction_product_id
     * @return DataObject
     */
    protected function getHighestPriceEntity(int $auction_product_id): DataObject
    {
        return $this->auctionProduct->getHighestPriceEntity($auction_product_id);
    }

    /**
     * @param int $auction_product_id
     * @return float
     */
    public function getHighestPrice(int $auction_product_id): float
    {
        return $this->auctionProduct->getHighestPrice($auction_product_id);
    }

    /**
     * @return array|string[]
     */
    public function getIdentities(): array
    {
        $data = [];

        foreach ($this->getProductList() as $item) {
            $bidder = $this->getHighestPriceEntity($item->getData('entity_id'));

            $data[] = Product::CACHE_TAG . '_' . $item->getData('product_id');
            $data[] = AuctionModel::CACHE_TAG . '_' . $item->getData('auction_id');
            $data[] = AuctionBidder::CACHE_TAG . '_' . $bidder->getData('entity_id');
        }

        return $data;
    }
}
