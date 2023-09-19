<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Customer;

use DevHongZui\AuctionProducts\Model\AuctionBidder;
use DevHongZui\AuctionProducts\Model\ResourceModel\Auction;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionBidder\Collection;
use DevHongZui\AuctionProducts\Model\ResourceModel\AuctionBidder\CollectionFactory as AuctionBidderCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class WinningProducts extends Template
{
    protected Session $session;

    protected AuctionBidderCollectionFactory $auctionBidderCollectionFactory;

    /**
     * @param Context $context
     * @param Session $session
     * @param AuctionBidderCollectionFactory $auctionBidderCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context               $context,
        Session                        $session,
        AuctionBidderCollectionFactory $auctionBidderCollectionFactory,
        array                          $data = []
    )
    {
        parent::__construct($context, $data);

        $this->session = $session;
        $this->auctionBidderCollectionFactory = $auctionBidderCollectionFactory;
    }

    /**
     * @return Collection
     */
    public function getList(): Collection
    {
        $customer_id = $this->session->getCustomerId();

        $auction_bidder_collection = $this->auctionBidderCollectionFactory->create();

        $auction_bidder_collection
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('bid_status', [
                AuctionBidder::STATUS_WIN,
                AuctionBidder::STATUS_BOUGHT,
                AuctionBidder::STATUS_EXPIRED
            ])
            ->getSelect()
            ->joinLeft(
                ['e' => Auction::TABLE_NAME],
                'main_table.auction_id = e.entity_id',
                ['days']
            );

        return $auction_bidder_collection;
    }
}
