<?php

namespace DevHongZui\AuctionProducts\Helper;

use DevHongZui\AuctionProducts\Model\Auction;
use DevHongZui\AuctionProducts\Model\AuctionProduct;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PricingHelperData;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Data extends AbstractHelper
{
    protected TimezoneInterface $timezone;

    protected SessionFactory $sessionFactory;

    protected Url $url;

    protected Auction $auction;
    protected AuctionProduct $auctionProduct;

    protected PricingHelperData $pricingHelperData;

    /**
     * @param TimezoneInterface $timezone
     * @param SessionFactory $sessionFactory
     * @param Url $url
     * @param Auction $auction
     * @param AuctionProduct $auctionProduct
     * @param PricingHelperData $pricingHelperData
     * @param Context $context
     */
    public function __construct(
        TimezoneInterface $timezone,
        SessionFactory    $sessionFactory,
        Url               $url,
        Auction           $auction,
        AuctionProduct    $auctionProduct,
        PricingHelperData $pricingHelperData,
        Context           $context
    )
    {
        parent::__construct($context);

        $this->timezone = $timezone;
        $this->sessionFactory = $sessionFactory;
        $this->url = $url;
        $this->auction = $auction;
        $this->auctionProduct = $auctionProduct;
        $this->pricingHelperData = $pricingHelperData;
    }

    /**
     * @return string
     */
    public function getCurrentTime(): string
    {
        return $this->timezone->date()->format('Y-m-d H:i:s');
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
    public function getLoginUrl(): string
    {
        return $this->url->getLoginUrl();
    }

    /**
     * @param int $auction_id
     * @return Auction
     */
    public function getAuction(int $auction_id): Auction
    {
        return $this->auction->load($auction_id);
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
     * @param int $product_id
     * @return float
     * @throws NoSuchEntityException
     */
    public function getHighestPriceByProductId(int $product_id): float
    {
        return $this->auctionProduct->getHighestPriceByProductId($product_id);
    }

    /**
     * @param $price
     * @return string
     */
    public function formatPrice($price): string
    {
        return $this->pricingHelperData->currency($price, includeContainer: false);
    }
}
