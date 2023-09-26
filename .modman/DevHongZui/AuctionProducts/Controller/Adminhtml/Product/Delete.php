<?php

namespace DevHongZui\AuctionProducts\Controller\Adminhtml\Product;

use DevHongZui\AuctionProducts\Model\AuctionFactory;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;

class Delete extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    protected AuctionFactory $auctionFactory;

    /**
     * @param Context $context
     * @param AuctionFactory $auctionFactory
     */
    public function __construct(
        Context        $context,
        AuctionFactory $auctionFactory
    )
    {
        parent::__construct($context);

        $this->auctionFactory = $auctionFactory;
    }

    /**
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface
    {
        try {
            $auction_model = $this->auctionFactory->create();

            $auction_model
                ->load($this->getRequest()->getParam('id'))
                ->delete();

            $this->messageManager->addSuccessMessage(__(
                'Data Deleted Successfully.'
            ));

            return $this->_redirect('*/*/');
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__(
                "We can't submit your request, Please try again. (%1)",
                $exception->getMessage()
            ));

            return $this->_redirect(
                '*/*/edit', [
                'id' => $this->getRequest()->getParam('id')
            ]);
        }
    }
}
