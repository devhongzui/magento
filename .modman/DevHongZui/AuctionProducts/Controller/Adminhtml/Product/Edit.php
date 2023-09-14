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
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    protected PageFactory $resultPageFactory;

    protected AuctionFactory $auctionFactory;

    protected DataPersistorInterface $dataPersistor;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AuctionFactory $auctionFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context                $context,
        PageFactory            $resultPageFactory,
        AuctionFactory         $auctionFactory,
        DataPersistorInterface $dataPersistor
    )
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->auctionFactory = $auctionFactory;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return Page
     */
    public function execute(): Page
    {
        $id = $this->getRequest()->getParam('id');

        $this->dataPersistor->set('auction_id', $id);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Backend::content_elements');
        $resultPage->getConfig()->getTitle()->prepend($this->getTitle($id));
        return $resultPage;
    }

    /**
     * @param int|null $id
     * @return string
     */
    protected function getTitle(?int $id): string
    {
        if (is_null($id))
            return __('New Auction Event');

        $auction_model = $this->auctionFactory->create();

        $auction_model->load($id);

        $auction_id = $auction_model->getId();

        return $auction_id
            ? __('Auction ID %1', $auction_id)
            : __("This auction event doesn't exist.");
    }
}
