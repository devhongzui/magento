<?php
/**
 * Copyright © @2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

abstract class Generic implements ButtonProviderInterface
{
    protected Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
