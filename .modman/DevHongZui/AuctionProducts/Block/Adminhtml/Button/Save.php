<?php
/**
 * Copyright © @2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Button;

use Magento\Ui\Component\Control\Container;

class Save extends Generic
{
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $id = $this->getModelId();

        if (
            ($id && $this->haveSave($id)) ||
            !$id
        )
            return [
                'label' => __('Save'),
                'class' => 'save primary',
                'class_name' => Container::DEFAULT_CONTROL,
                'data_attribute' => [
                    'form-role' => 'save',
                    'mage-init' => [
                        'button' => ['event' => 'save'],
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

        return [];
    }
}
