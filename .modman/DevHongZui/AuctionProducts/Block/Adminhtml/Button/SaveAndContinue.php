<?php
/**
 * Copyright © @2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Block\Adminhtml\Button;

use Magento\Ui\Component\Control\Container;

class SaveAndContinue extends Generic
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
            if ($this->auction->haveSave($id))
                return [
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

        return [];
    }
}
