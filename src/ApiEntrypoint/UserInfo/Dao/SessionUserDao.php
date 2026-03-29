<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\ApiEntrypoint\UserInfo\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

readonly class SessionUserDao implements SessionUserDaoInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {
    }

    public function getFirstNameByUsername(string $username): ?string
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('oxfname')
            ->from('oxuser')
            ->where('oxusername = :username')
            ->andWhere('oxactive = 1')
            ->setParameter('username', $username);

        $result = $queryBuilder->execute()->fetchOne();

        return $result !== false ? (string) $result : null;
    }
}
