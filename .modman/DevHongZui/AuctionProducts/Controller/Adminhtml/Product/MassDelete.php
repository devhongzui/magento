<?php

namespace DevHongZui\AuctionProducts\Controller\Adminhtml\Product;

use DevHongZui\AuctionProducts\Model\ResourceModel\Auction\CollectionFactory as AuctionCollectionFactory;
use Exception;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;

class MassDelete extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    protected AuctionCollectionFactory $auctionCollectionFactory;

    protected Filter $filter;

    /**
     * @param Context $context
     * @param AuctionCollectionFactory $auctionCollectionFactory
     * @param Filter $filter
     */
    public function __construct(
        Context                  $context,
        AuctionCollectionFactory $auctionCollectionFactory,
        Filter                   $filter
    )
    {
        parent::__construct($context);

        $this->auctionCollectionFactory = $auctionCollectionFactory;
        $this->filter = $filter;
    }

    /**
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface
    {
        try {
            $filter = $this->filter->getCollection(
                $this->auctionCollectionFactory->create()
            );

            $count = 0;

            foreach ($filter as $child) {
                $child->delete();

                $count++;
            }

            $this->messageManager->addSuccessMessage(__(
                'A total of %1 record(s) have been deleted.', $count
            ));

        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__(
                "We can't submit your request, Please try again. (%1)",
                $exception->getMessage()
            ));
        }

        return $this->_redirect('*/*/');
    }
}
