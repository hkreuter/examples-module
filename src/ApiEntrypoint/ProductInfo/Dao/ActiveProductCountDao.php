<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\ApiEntrypoint\ProductInfo\Dao;

use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

readonly class ActiveProductCountDao implements ActiveProductCountDaoInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private TableViewNameGenerator $tableViewNameGenerator,
    ) {
    }

    public function getActiveProductCount(): int
    {
        $table = $this->tableViewNameGenerator->getViewName('oxarticles');

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('COUNT(*)')
            ->from($table)
            ->where('oxactive = 1')
            ->andWhere('oxparentid = :parentId')
            ->setParameter('parentId', '');

        return (int) $queryBuilder->execute()->fetchOne();
    }
}
