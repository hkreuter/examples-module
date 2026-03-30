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

readonly class PromotedProductSearchService implements ProductSearchServiceInterface
{
    public function __construct(
        private string $promotedProductId,
    ) {
    }

    public function search(
        ProductSearchCriteria $criteria,
        array $context = [],
    ): ProductSearchResult {
        if ($this->promotedProductId === '') {
            return new ProductSearchResult([], 0);
        }

        return new ProductSearchResult(
            [Id::fromString($this->promotedProductId)],
            1,
        );
    }
}
