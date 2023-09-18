<?php
/**
 * Copyright © 2023 All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Validator\ValidateException;

class AddAuctionIdProductAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private EavSetupFactory $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            'auction_id', [
            'type' => 'int',
            'label' => 'Auction ID',
            'input' => 'text',
            'source' => '',
            'frontend' => '',
            'required' => false,
            'backend' => '',
            'sort_order' => '100',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'default' => 0,
            'visible' => false,
            'user_defined' => false,
            'searchable' => false,
            'filterable' => true,
            'comparable' => false,
            'unique' => false,
            'apply_to' => '',
            'group' => 'General',
            'is_used_in_grid' => false,
            'visible_on_front' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'used_in_product_listing' => true,
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
