<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Unit\ProductSearch\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchResult;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Pagination;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SearchTerm;
use OxidEsales\ExamplesModule\ProductSearch\Dao\SimpleProductSearchDaoInterface;
use OxidEsales\ExamplesModule\ProductSearch\Service\PromotedProductSearchService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PromotedProductSearchService::class)]
final class PromotedProductSearchServiceTest extends TestCase
{
    public function testPromotedProductAppearsFirst(): void
    {
        $promotedId = uniqid('promoted_', true);
        $regularId = uniqid('regular_', true);

        $sut = $this->getSut(
            promotedProductId: $promotedId,
            innerResult: new ProductSearchResult(
                [Id::fromString($regularId)],
                1
            ),
        );

        $result = $sut->search($this->createCriteria('test'));

        $ids = array_map('strval', $result->getProductIds());
        $this->assertSame($promotedId, $ids[0]);
        $this->assertSame($regularId, $ids[1]);
    }

    public function testPromotedProductIncreasesTotalWhenNotInResults(): void
    {
        $promotedId = uniqid('promoted_', true);

        $sut = $this->getSut(
            promotedProductId: $promotedId,
            innerResult: new ProductSearchResult(
                [Id::fromString(uniqid())],
                1
            ),
        );

        $result = $sut->search($this->createCriteria('test'));

        $this->assertSame(2, $result->getTotal());
    }

    public function testPromotedProductMovesToFirstWhenAlreadyInResults(): void
    {
        $promotedId = uniqid('promoted_', true);
        $otherId = uniqid('other_', true);

        $sut = $this->getSut(
            promotedProductId: $promotedId,
            innerResult: new ProductSearchResult(
                [Id::fromString($otherId), Id::fromString($promotedId)],
                2
            ),
        );

        $result = $sut->search($this->createCriteria('test'));

        $ids = array_map('strval', $result->getProductIds());
        $this->assertSame($promotedId, $ids[0]);
        $this->assertSame($otherId, $ids[1]);
        $this->assertSame(2, $result->getTotal());
    }

    public function testEmptyPromotedIdReturnsResultsUnchanged(): void
    {
        $regularId = uniqid('regular_', true);

        $sut = $this->getSut(
            promotedProductId: '',
            innerResult: new ProductSearchResult(
                [Id::fromString($regularId)],
                1
            ),
        );

        $result = $sut->search($this->createCriteria('test'));

        $ids = array_map('strval', $result->getProductIds());
        $this->assertSame([$regularId], $ids);
        $this->assertSame(1, $result->getTotal());
    }

    public function testEmptySearchResultsStillShowPromotedProduct(): void
    {
        $promotedId = uniqid('promoted_', true);

        $sut = $this->getSut(
            promotedProductId: $promotedId,
            innerResult: new ProductSearchResult([], 0),
        );

        $result = $sut->search($this->createCriteria('anything'));

        $ids = array_map('strval', $result->getProductIds());
        $this->assertSame([$promotedId], $ids);
        $this->assertSame(1, $result->getTotal());
    }

    public function testDelegateReceivesCriteria(): void
    {
        $criteria = $this->createCriteria('searchterm');

        $daoSpy = $this->createMock(SimpleProductSearchDaoInterface::class);
        $daoSpy->expects($this->once())
            ->method('search')
            ->with($criteria)
            ->willReturn(new ProductSearchResult([], 0));

        $sut = new PromotedProductSearchService(
            searchDao: $daoSpy,
            promotedProductId: '',
        );

        $sut->search($criteria);
    }

    private function getSut(
        string $promotedProductId,
        ProductSearchResult $innerResult,
    ): PromotedProductSearchService {
        $daoStub = $this->createStub(SimpleProductSearchDaoInterface::class);
        $daoStub->method('search')
            ->willReturn($innerResult);

        return new PromotedProductSearchService(
            searchDao: $daoStub,
            promotedProductId: $promotedProductId,
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
