<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\ProductInfo\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

readonly class ActiveProductCountDao implements ActiveProductCountDaoInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {
    }

    public function getActiveProductCount(): int
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('COUNT(*)')
            ->from('oxarticles')
            ->where('oxactive = 1')
            ->andWhere('oxparentid = :parentId')
            ->setParameter('parentId', '');

        return (int) $queryBuilder->execute()->fetchOne();
    }
}
