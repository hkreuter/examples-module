<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\ProductSearch\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchResult;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

readonly class SimpleProductSearchDao implements SimpleProductSearchDaoInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {
    }

    public function search(ProductSearchCriteria $criteria): ProductSearchResult
    {
        $term = $criteria->getTerm()->getValue();

        if ($term === '') {
            return new ProductSearchResult([], 0);
        }

        $total = $this->countResults($term);

        if ($total === 0) {
            return new ProductSearchResult([], 0);
        }

        $ids = $this->fetchIds($criteria, $term);

        return new ProductSearchResult($ids, $total);
    }

    private function countResults(string $term): int
    {
        $qb = $this->queryBuilderFactory->create();
        $qb->select('COUNT(*)')
            ->from('oxarticles')
            ->where('oxactive = 1')
            ->andWhere('oxparentid = :parentId')
            ->andWhere('oxissearch = 1')
            ->andWhere(
                $qb->expr()->or(
                    'oxtitle LIKE :term',
                    'oxsearchkeys LIKE :term',
                    'oxartnum LIKE :term',
                )
            )
            ->setParameter('parentId', '')
            ->setParameter('term', '%' . $term . '%');

        return (int) $qb->execute()->fetchOne();
    }

    /** @return list<Id> */
    private function fetchIds(
        ProductSearchCriteria $criteria,
        string $term,
    ): array {
        $qb = $this->queryBuilderFactory->create();
        $qb->select('oxid')
            ->from('oxarticles')
            ->where('oxactive = 1')
            ->andWhere('oxparentid = :parentId')
            ->andWhere('oxissearch = 1')
            ->andWhere(
                $qb->expr()->or(
                    'oxtitle LIKE :term',
                    'oxsearchkeys LIKE :term',
                    'oxartnum LIKE :term',
                )
            )
            ->setParameter('parentId', '')
            ->setParameter('term', '%' . $term . '%')
            ->setFirstResult($criteria->getPagination()->getOffset())
            ->setMaxResults($criteria->getPagination()->getLimit());

        $sorting = $criteria->getSorting();
        if ($sorting) {
            $qb->orderBy(
                $sorting[0]->getField(),
                $sorting[0]->getDirection()->value,
            );
        }

        $rows = $qb->execute()->fetchFirstColumn();

        return array_map(
            static fn(string $id) => Id::fromString($id),
            $rows,
        );
    }
}
