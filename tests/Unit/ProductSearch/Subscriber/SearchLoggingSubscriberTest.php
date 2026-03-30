<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Unit\ProductSearch\Subscriber;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\AfterProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\BeforeProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchResult;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Pagination;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SearchTerm;
use OxidEsales\ExamplesModule\ProductSearch\Subscriber\SearchLoggingSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(SearchLoggingSubscriber::class)]
final class SearchLoggingSubscriberTest extends TestCase
{
    public function testOnBeforeSearchLogsSearchTerm(): void
    {
        $searchTerm = uniqid('term_', true);

        $loggerSpy = $this->createMock(LoggerInterface::class);
        $loggerSpy->expects($this->once())
            ->method('info')
            ->with(
                $this->stringContains($searchTerm)
            );

        $sut = new SearchLoggingSubscriber($loggerSpy);

        $criteria = new ProductSearchCriteria(
            new Pagination(10, 0),
            new SearchTerm($searchTerm),
        );
        $event = new BeforeProductSearchEvent($criteria);

        $sut->onBeforeSearch($event);
    }

    public function testOnAfterSearchLogsResultCount(): void
    {
        $total = mt_rand(1, 500);

        $loggerSpy = $this->createMock(LoggerInterface::class);
        $loggerSpy->expects($this->once())
            ->method('info')
            ->with(
                $this->stringContains((string) $total)
            );

        $sut = new SearchLoggingSubscriber($loggerSpy);

        $criteria = new ProductSearchCriteria(
            new Pagination(10, 0),
            new SearchTerm(uniqid()),
        );
        $result = new ProductSearchResult([], $total);
        $event = new AfterProductSearchEvent($criteria, [], $result);

        $sut->onAfterSearch($event);
    }

    public function testGetSubscribedEventsReturnsExpectedEvents(): void
    {
        $events = SearchLoggingSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(BeforeProductSearchEvent::class, $events);
        $this->assertArrayHasKey(AfterProductSearchEvent::class, $events);
    }
}
