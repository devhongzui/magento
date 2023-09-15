<?php

namespace DevHongZui\AuctionProducts\Controller\Adminhtml\Product;

use DevHongZui\AuctionProducts\Model\AuctionFactory;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey\Validator;

class Save extends Action implements HttpPostActionInterface
{
    protected DataPersistorInterface $dataPersistor;

    protected Validator $validator;

    protected AuctionFactory $auctionFactory;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param Validator $validator
     * @param AuctionFactory $auctionFactory
     */
    public function __construct(
        Context                $context,
        DataPersistorInterface $dataPersistor,
        Validator              $validator,
        AuctionFactory         $auctionFactory
    )
    {
        parent::__construct($context);

        $this->dataPersistor = $dataPersistor;
        $this->validator = $validator;
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
                ->setData($this->validate())
                ->save();

            $this->messageManager->addSuccessMessage(__(
                'Data Saved Successfully.'
            ));

            return $this->_redirect(
                '*/*/edit',
                ['id' => $auction_model->getId()]
            );
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__(
                "We can't submit your request, Please try again. (%1)",
                $exception->getMessage()
            ));

            $this->dataPersistor->set('auction', $this->getRequest()->getParams());

            return $this->_redirect(
                '*/*/edit',
                ['id' => $this->getRequest()->getParam('entity_id')]
            );
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function validate(): array
    {
        if (!$this->getRequest()->getParams())
            throw new Exception(__('Data is Empty!'));

        if (!$this->validator->validate($this->getRequest()))
            throw new Exception(__('Form Key is Empty!'));

        $general = $this->getRequest()->getParam('general');

        return [
            'entity_id' => $general['entity_id'] ?? null,
            'new_product_ids' => $general['product_ids'],
            'start_price' => $general['start_price'],
            'step_price' => $general['step_price'],
            'reserve_price' => $general['reserve_price'],
            'limit_price' => $general['limit_price'],
            'start_at' => $general['start_at'],
            'stop_at' => $general['stop_at'],
            'days' => $general['days'],
            'status' => $general['status'],
        ];
    }
}
