<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{
    protected PageFactory $factory;

    /**
     * @param Context $context
     * @param PageFactory $factory
     */
    public function __construct(
        Context     $context,
        PageFactory $factory
    )
    {
        parent::__construct($context);

        $this->factory = $factory;
    }

    /**
     * @return Page
     */
    public function execute(): Page
    {
        /**  @var Page $resultPage */
        $resultPage = $this->factory->create();
        $resultPage->setActiveMenu('DevHongZui_AuctionProducts::auction_product_manage');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Management'));
        return $resultPage;
    }
}
