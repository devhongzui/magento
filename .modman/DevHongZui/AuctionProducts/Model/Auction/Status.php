<?php

namespace DevHongZui\AuctionProducts\Model\Auction;

use DevHongZui\AuctionProducts\Model\Auction;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Status extends AbstractSource
{
    protected Auction $auction;

    /**
     * @param Auction $auction
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
    }

    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        if (is_null($this->_options)) {
            $this->_options = [];

            foreach ($this->auction->getAllStatus() as $value => $label)
                $this->_options[] = ['label' => $label, 'value' => $value];
        }

        return $this->_options;
    }
}
