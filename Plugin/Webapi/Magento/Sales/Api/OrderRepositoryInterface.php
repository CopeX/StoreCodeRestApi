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

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Api\Search\FilterGroup  $filterGroup
     * @param \Magento\Framework\Api\Filter              $filter
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter
    ) {
        $this->storeManager = $storeManager;
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;
    }

    public function beforeGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $this->filter->setField('store_id');
        $this->filter->setConditionType('eq');
        $this->filter->setValue($this->storeManager->getStore()->getId());
        $storeFilterGroup = $this->filterGroup->setFilters([$this->filter]);
        $groups = $searchCriteria->getFilterGroups();
        $groups[] = $storeFilterGroup;
        $searchCriteria->setFilterGroups($groups);
        return [$searchCriteria];
    }
}

