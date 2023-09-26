<?php
/**
 * Copyright © @2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Button;

class Delete extends Generic
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $id = $this->getModelId();

        if ($id && $this->haveDelete($id))
            return [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s')",
                    __('Delete selected item?'),
                    $this->getUrl('*/*/delete', ['id' => $id])
                )
            ];

        return [];
    }
}
