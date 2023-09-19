<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Controller\Product;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class History extends AbstractAccount implements HttpGetActionInterface
{
    protected Session $session;

    protected Url $url;

    protected PageFactory $pageFactory;

    /**
     * @param Context $context
     * @param Session $session
     * @param Url $url
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context     $context,
        Session     $session,
        Url         $url,
        PageFactory $pageFactory
    )
    {
        parent::__construct($context);

        $this->session = $session;
        $this->url = $url;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return Redirect|Page
     */
    public function execute(): Redirect|Page
    {
        if ($this->session->isLoggedIn()) {
            $resultPage = $this->pageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('My Auction'));
            $resultPage->setHeader('Login-Required', 'true');
            return $resultPage;
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl(
            $this->url->getLoginUrl()
        );
        return $resultRedirect;
    }
}
