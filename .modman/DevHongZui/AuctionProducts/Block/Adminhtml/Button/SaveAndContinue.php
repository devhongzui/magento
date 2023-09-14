<?php
/**
 * Copyright © @2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Button;

use DevHongZui\AuctionProducts\Model\Auction;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\Component\Control\Container;

class SaveAndContinue extends Generic
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

        if (
            ($id && $this->auction->haveSave($id)) ||
            !$id
        )
            if ($this->auction->haveSave($id))
                $data = [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'class_name' => Container::DEFAULT_CONTROL,
                    'data_attribute' => [
                        'form-role' => 'save',
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit'],
                            'buttonAdapter' => [
                                'actions' => [
                                    [
                                        'targetName' => 'edit.edit',
                                        'actionName' => 'save',
                                        'params' => [false]
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

        return $data;
    }
}
