<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Model\AuctionBidder;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\ComponentVisibilityInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;

class BidderFieldset extends Fieldset implements ComponentVisibilityInterface
{
    protected RequestInterface $request;

    /**
     * @param ContextInterface $context
     * @param RequestInterface $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        RequestInterface $request,
        array            $components = [],
        array            $data = []
    )
    {
        parent::__construct($context, $components, $data);

        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isComponentVisible(): bool
    {
        $auction_id = $this->request->getParam('id');

        return isset($auction_id);
    }
}
