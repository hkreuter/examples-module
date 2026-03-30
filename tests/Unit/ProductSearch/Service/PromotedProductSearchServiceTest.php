<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Unit\ProductSearch\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Pagination;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SearchTerm;
use OxidEsales\ExamplesModule\ProductSearch\Service\PromotedProductSearchService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PromotedProductSearchService::class)]
final class PromotedProductSearchServiceTest extends TestCase
{
    public function testReturnsPromotedProductId(): void
    {
        $promotedId = uniqid('promoted_', true);
        $sut = new PromotedProductSearchService($promotedId);

        $result = $sut->search($this->createCriteria('anything'));

        $ids = array_map('strval', $result->getProductIds());
        $this->assertSame([$promotedId], $ids);
    }

    public function testReturnsTotalOfOneWhenPromotedIdSet(): void
    {
        $sut = new PromotedProductSearchService(uniqid());

        $result = $sut->search($this->createCriteria('anything'));

        $this->assertSame(1, $result->getTotal());
    }

    public function testReturnsEmptyResultWhenNoPromotedId(): void
    {
        $sut = new PromotedProductSearchService('');

        $result = $sut->search($this->createCriteria('anything'));

        $this->assertSame([], $result->getProductIds());
        $this->assertSame(0, $result->getTotal());
    }

    public function testIgnoresSearchTermAndAlwaysReturnsPromotedProduct(): void
    {
        $promotedId = uniqid('promoted_', true);
        $sut = new PromotedProductSearchService($promotedId);

        $result1 = $sut->search($this->createCriteria('kite'));
        $result2 = $sut->search($this->createCriteria('shoe'));

        $this->assertSame(
            (string) $result1->getProductIds()[0],
            (string) $result2->getProductIds()[0]
        );
    }

    private function createCriteria(string $term): ProductSearchCriteria
    {
        return new ProductSearchCriteria(
            new Pagination(10, 0),
            new SearchTerm($term),
        );
    }
}
