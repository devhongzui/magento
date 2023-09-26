<?php
/**
 * Copyright © @2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Button;

use DevHongZui\AuctionProducts\Model\Auction;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

abstract class Generic implements ButtonProviderInterface
{
    protected Context $context;

    protected Auction $auction;

    /**
     * @param Context $context
     * @param Auction $auction
     */
    public function __construct(
        Context $context,
        Auction $auction
    )
    {
        $this->context = $context;
        $this->auction = $auction;
    }

    /**
     * @return int|null
     */
    protected function getModelId(): int|null
    {
        return $this->context->getRequest()->getParam('id');
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

    /**
     * @param int $id
     * @return bool
     */
    protected function haveSave(int $id): bool
    {
        return is_bool($this->auction->haveSave($id));
    }

    /**
     * @param int $id
     * @return bool
     */
    protected function haveDelete(int $id): bool
    {
        return is_bool($this->auction->haveDelete($id));
    }
}
