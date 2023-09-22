<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Controller\Adminhtml\Product;

use DevHongZui\AuctionProducts\Model\AuctionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    protected PageFactory $resultPageFactory;

    protected AuctionFactory $auctionFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AuctionFactory $auctionFactory
     */
    public function __construct(
        Context        $context,
        PageFactory    $resultPageFactory,
        AuctionFactory $auctionFactory
    )
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->auctionFactory = $auctionFactory;
    }

    /**
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('DevHongZui_AuctionProducts::auction_product_manage');
        $resultPage->getConfig()->getTitle()->prepend($this->getTitle());
        return $resultPage;
    }

    /**
     * @return string
     */
    protected function getTitle(): string
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $auction_model = $this->auctionFactory->create();

            $auction_id = $auction_model->load($id)->getId();

            return $auction_id
                ? __('Auction ID %1', $auction_id)
                : __("This auction doesn't exist");
        }

        return __('New Auction');
    }
}
