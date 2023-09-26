<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DevHongZui\AuctionProducts\Controller\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Controller\Category\View;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager;
use Magento\Catalog\Model\Design;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Catalog\Model\Session;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\Page\Interceptor;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Listing extends View implements HttpGetActionInterface
{
    private ToolbarMemorizer $toolbarMemorizer;

    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param Design $catalogDesign
     * @param Session $catalogSession
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ToolbarMemorizer|null $toolbarMemorizer
     * @param LayoutUpdateManager|null $layoutUpdateManager
     * @param CategoryHelper|null $categoryHelper
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context                     $context,
        Design                      $catalogDesign,
        Session                     $catalogSession,
        Registry                    $coreRegistry,
        StoreManagerInterface       $storeManager,
        CategoryUrlPathGenerator    $categoryUrlPathGenerator,
        PageFactory                 $resultPageFactory,
        ForwardFactory              $resultForwardFactory,
        Resolver                    $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        ToolbarMemorizer            $toolbarMemorizer = null,
        ?LayoutUpdateManager        $layoutUpdateManager = null,
        CategoryHelper              $categoryHelper = null,
        LoggerInterface             $logger = null
    )
    {
        parent::__construct(
            $context,
            $catalogDesign,
            $catalogSession,
            $coreRegistry,
            $storeManager,
            $categoryUrlPathGenerator,
            $resultPageFactory,
            $resultForwardFactory,
            $layerResolver,
            $categoryRepository,
            $toolbarMemorizer,
            $layoutUpdateManager,
            $categoryHelper,
            $logger
        );

        $this->toolbarMemorizer = ObjectManager::getInstance()->get(ToolbarMemorizer::class);
        $this->logger = ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * @return bool|CategoryInterface
     */
    protected function _initCategory(): bool|CategoryInterface
    {
        try {
            $categoryId = $this->_storeManager->getStore()->getRootCategoryId();
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical($exception);
            return false;
        }

        try {
            $category = $this->categoryRepository->get(
                $categoryId,
                $this->_storeManager->getStore()->getId()
            );
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical($exception);
            return false;
        }

        $this->_catalogSession->setLastVisitedCategoryId($category->getId());
        $this->_coreRegistry->register('current_category', $category);
        $this->toolbarMemorizer->memorizeParams();
        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after', [
                'category' => $category,
                'controller_action' => $this
            ]);
        } catch (LocalizedException $exception) {
            $this->logger->critical($exception);
            return false;
        }

        return $category;
    }

    /**
     * @return ResponseInterface|Forward|Redirect|ResultInterface|Page|null
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    public function execute(): Page|ResultInterface|ResponseInterface|Forward|Redirect|null
    {
        $result = parent::execute();

        if (get_class($result) == Interceptor::class)
            $result->getConfig()->getTitle()->set(__('Auction Products'));

        return $result;
    }
}
