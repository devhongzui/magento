<?php

namespace DevHongZui\AuctionProducts\Plugin\Layer;

use DevHongZui\AuctionProducts\Model\Auction\Filter;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Framework\App\ObjectManager;

class FilterList
{
    /**
     * @param Layer\FilterList $subject
     * @param array|AbstractFilter[] $result
     * @param Layer $layer
     * @return array
     */
    public function afterGetFilters(Layer\FilterList $subject, array $result, Layer $layer): array
    {
        return array_merge(
            $result, [
            ObjectManager::getInstance()->create(Filter::class, ['layer' => $layer])
        ]);
    }
}
