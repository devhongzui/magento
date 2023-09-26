<?php

namespace DevHongZui\AuctionProducts\Ui\Component\Listing\Column;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CustomerName extends Column
{
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface            $context,
        UiComponentFactory          $uiComponentFactory,
        CustomerRepositoryInterface $customerRepository,
        array                       $components = [],
        array                       $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->customerRepository = $customerRepository;
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']))
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getName();
                $item[$name] = $this->getCustomerName($item[$name]);
            }

        return $dataSource;
    }

    /**
     * @param int $customer_id
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCustomerName(int $customer_id): string
    {
        return $this->customerRepository->getById($customer_id)->getFirstname();
    }
}
