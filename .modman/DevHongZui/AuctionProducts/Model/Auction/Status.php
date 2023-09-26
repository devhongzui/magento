<?php

namespace DevHongZui\AuctionProducts\Model\Auction;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Status extends AbstractSource
{
    const STATUS_NOT_START = 0;

    const STATUS_PROCESSING = 1;

    const STATUS_FINISHED = 2;

    const STATUS_CLOSED = 3;

    const STATUS_DISABLED = 4;

    const STATUS_ENDED = 5;

    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        if (empty($this->_options))
            $this->_options = [
                ['value' => self::STATUS_NOT_START, 'label' => __('Not Start')],
                ['value' => self::STATUS_PROCESSING, 'label' => __('Processing')],
                ['value' => self::STATUS_FINISHED, 'label' => __('Finished')],
                ['value' => self::STATUS_CLOSED, 'label' => __('Closed')],
                ['value' => self::STATUS_DISABLED, 'label' => __('Disabled')],
                ['value' => self::STATUS_ENDED, 'label' => __('End')]
            ];

        return $this->_options;
    }
}
