<?php

namespace DevHongZui\AuctionProducts\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class AuctionBidder extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'devhongzui_auctionProducts_auctionBidder';

    const STATUS_CANCELED = 0;

    const STATUS_HIGHEST = 1;

    const STATUS_OVERED = 2;

    const STATUS_WIN = 3;

    const STATUS_LOSE = 4;

    const STATUS_BOUGHT = 5;

    const STATUS_EXPIRED = 6;

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = self::CACHE_TAG;

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->_init(ResourceModel\AuctionBidder::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [$this->_cacheTag . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getAllStatus(): array
    {
        return [
            self::STATUS_CANCELED => __('Canceled'),
            self::STATUS_HIGHEST => __('Highest'),
            self::STATUS_OVERED => __('Overed'),
            self::STATUS_WIN => __('Win'),
            self::STATUS_LOSE => __('Lose'),
            self::STATUS_BOUGHT => __('Bought'),
            self::STATUS_EXPIRED => __('Expired')
        ];
    }

    /**
     * @param int|null $status
     * @return string
     */
    public function getStatusLabel(int $status = null): string
    {
        $labels = $this->getAllStatus();

        return is_null($status)
            ? $labels[$this->getData('status')]
            : $labels[$status];
    }
}
