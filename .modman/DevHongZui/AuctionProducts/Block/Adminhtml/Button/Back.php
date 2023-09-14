<?php
/**
 * Copyright © @2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Button;

class Back extends Generic
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'url' => $this->getUrl('*/*/'),
            'class' => 'back',
        ];
    }
}
