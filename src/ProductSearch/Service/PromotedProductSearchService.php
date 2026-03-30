<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\ProductSearch\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchResult;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\ExamplesModule\ProductSearch\Dao\SimpleProductSearchDaoInterface;

readonly class PromotedProductSearchService implements ProductSearchServiceInterface
{
    public function __construct(
        private SimpleProductSearchDaoInterface $searchDao,
        private string $promotedProductId,
    ) {
    }

    public function search(
        ProductSearchCriteria $criteria,
        array $context = [],
    ): ProductSearchResult {
        $result = $this->searchDao->search($criteria);

        if ($this->promotedProductId === '') {
            return $result;
        }

        return $this->prependPromotedProduct($result);
    }

    private function prependPromotedProduct(
        ProductSearchResult $result,
    ): ProductSearchResult {
        $ids = $result->getProductIds();
        $total = $result->getTotal();

        $filteredIds = array_filter(
            $ids,
            fn(Id $id) => (string) $id !== $this->promotedProductId,
        );

        $wasAlreadyPresent = count($filteredIds) < count($ids);

        array_unshift($filteredIds, Id::fromString($this->promotedProductId));

        return new ProductSearchResult(
            array_values($filteredIds),
            $wasAlreadyPresent ? $total : $total + 1,
        );
    }
}
