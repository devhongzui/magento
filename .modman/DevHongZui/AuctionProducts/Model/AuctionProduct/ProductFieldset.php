<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Model\AuctionProduct;

use Magento\Framework\View\Element\ComponentVisibilityInterface;
use Magento\Ui\Component\Form\Fieldset;

class ProductFieldset extends Fieldset implements ComponentVisibilityInterface
{
    /**
     * @return bool
     */
    public function isComponentVisible(): bool
    {
        $auction_id = $this->context->getRequestParam('id');

        return isset($auction_id);
    }
}
