<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\ProductSearch\Subscriber;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\AfterProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\BeforeProductSearchEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchLoggingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeProductSearchEvent::class => 'onBeforeSearch',
            AfterProductSearchEvent::class => 'onAfterSearch',
        ];
    }

    public function onBeforeSearch(BeforeProductSearchEvent $event): void
    {
        $this->logger->info(
            sprintf(
                'Custom product search for term: "%s"',
                $event->getSearchCriteria()->getTerm()->getValue()
            )
        );
    }

    public function onAfterSearch(AfterProductSearchEvent $event): void
    {
        $this->logger->info(
            sprintf(
                'Custom product search returned %d results',
                $event->getSearchResult()->getTotal()
            )
        );
    }
}
