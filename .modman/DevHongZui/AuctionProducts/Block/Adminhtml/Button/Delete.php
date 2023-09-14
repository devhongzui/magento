<?php
/**
 * Copyright © @2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Button;

use DevHongZui\AuctionProducts\Model\Auction;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;

class Delete extends Generic
{
    protected RequestInterface $request;

    protected Auction $auction;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param Auction $auction
     */
    public function __construct(
        Context          $context,
        RequestInterface $request,
        Auction          $auction
    )
    {
        parent::__construct($context);

        $this->request = $request;
        $this->auction = $auction;
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];

        $id = $this->request->getParam('id');

        if ($id)
            if ($this->auction->haveDelete($id))
                $data = [
                    'label' => __('Delete Auction'),
                    'class' => 'delete',
                    'on_click' => sprintf(
                        "deleteConfirm('%s', '%s')",
                        __('Delete selected item?'),
                        $this->getDeleteUrl($id)
                    )
                ];

        return $data;
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDeleteUrl(int $id): string
    {
        return $this->getUrl('*/*/delete', ['id' => $id]);
    }
}