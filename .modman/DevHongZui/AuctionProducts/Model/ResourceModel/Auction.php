<?php

namespace DevHongZui\AuctionProducts\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Auction extends AbstractDb
{
    const TABLE_NAME = 'devhongzui_auctions';

    const TABLE_ID = 'entity_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::TABLE_ID);
    }
}
