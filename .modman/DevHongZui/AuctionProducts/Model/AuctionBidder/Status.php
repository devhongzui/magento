<?php

namespace DevHongZui\AuctionProducts\Model\AuctionBidder;

use DevHongZui\AuctionProducts\Model\AuctionBidder;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Status extends AbstractSource
{
    protected AuctionBidder $auctionBidder;

    /**
     * @param AuctionBidder $auctionBidder
     */
    public function __construct(AuctionBidder $auctionBidder)
    {
        $this->auctionBidder = $auctionBidder;
    }

    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        if (is_null($this->_options)) {
            $this->_options = [];

            foreach ($this->auctionBidder->getAllStatus() as $value => $label)
                $this->_options[] = ['label' => $label, 'value' => $value];
        }

        return $this->_options;
    }
}
