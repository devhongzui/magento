<?php

namespace DevHongZui\AuctionProducts\Model\AuctionBidder;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Status extends AbstractSource
{
    const STATUS_CANCELED = 0;

    const STATUS_HIGHEST = 1;

    const STATUS_OVERED = 2;

    const STATUS_WIN = 3;

    const STATUS_LOSE = 4;

    const STATUS_BOUGHT = 5;

    const STATUS_EXPIRED = 6;

    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        if (empty($this->_options))
            $this->_options = [
                ['value' => self::STATUS_CANCELED, 'label' => __('Canceled')],
                ['value' => self::STATUS_HIGHEST, 'label' => __('Highest')],
                ['value' => self::STATUS_OVERED, 'label' => __('Overed')],
                ['value' => self::STATUS_WIN, 'label' => __('Win')],
                ['value' => self::STATUS_LOSE, 'label' => __('Lose')],
                ['value' => self::STATUS_BOUGHT, 'label' => __('Bought')],
                ['value' => self::STATUS_EXPIRED, 'label' => __('Expired')]
            ];

        return $this->_options;
    }
}
