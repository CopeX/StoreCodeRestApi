<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace CopeX\StoreCodeRestApi\Plugin\Webapi\Magento\Sales\Api;

class OrderRepositoryInterface
{
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \Magento\Framework\Api\Search\FilterGroup $filterGroup;
    private \Magento\Framework\Api\Filter $filter;
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Api\Search\FilterGroup  $filterGroup
     * @param \Magento\Framework\Api\Filter              $filter
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->storeManager = $storeManager;
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;
        $this->request = $request;
    }

    public function beforeGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $pathParts = $this->stripPathBeforeStorecode($this->request->getPathInfo());
        $storeCode = current($pathParts);
        $stores = $this->storeManager->getStores(false, true);
        if (isset($stores[$storeCode])) {
            $this->filter->setField('store_id');
            $this->filter->setConditionType('eq');
            $this->filter->setValue($this->storeManager->getStore()->getId());
            $storeFilterGroup = $this->filterGroup->setFilters([$this->filter]);
            $groups = $searchCriteria->getFilterGroups();
            $groups[] = $storeFilterGroup;
            $searchCriteria->setFilterGroups($groups);
        }
        return [$searchCriteria];
    }

    /**
     * Process path
     *
     * @param string $pathInfo
     * @return array
     */
    private function stripPathBeforeStorecode($pathInfo)
    {
        $pathParts = explode('/', trim($pathInfo, '/'));
        array_shift($pathParts);
        $path = '/' . implode('/', $pathParts);
        return explode('/', ltrim($path, '/'), 2);
    }
}

