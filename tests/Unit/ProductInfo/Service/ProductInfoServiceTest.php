<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Unit\ProductInfo\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\ExamplesModule\ProductInfo\Dao\ActiveProductCountDaoInterface;
use OxidEsales\ExamplesModule\ProductInfo\Service\ProductInfoService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProductInfoService::class)]
final class ProductInfoServiceTest extends TestCase
{
    public function testGetActiveProductCountDelegatesToDao(): void
    {
        $expectedCount = 42;

        $daoStub = $this->createStub(ActiveProductCountDaoInterface::class);
        $daoStub->method('getActiveProductCount')
            ->willReturn($expectedCount);

        $sut = $this->getSut(activeProductCountDao: $daoStub);

        $this->assertSame($expectedCount, $sut->getActiveProductCount());
    }

    public function testGetActiveProductCountReturnsZeroWhenNoProducts(): void
    {
        $daoStub = $this->createStub(ActiveProductCountDaoInterface::class);
        $daoStub->method('getActiveProductCount')
            ->willReturn(0);

        $sut = $this->getSut(activeProductCountDao: $daoStub);

        $this->assertSame(0, $sut->getActiveProductCount());
    }

    public function testGetGreetingMessageTranslatesLanguageConstant(): void
    {
        $expectedTranslation = 'Hello from OXID eShop API';

        $shopAdapterStub = $this->createStub(ShopAdapterInterface::class);
        $shopAdapterStub->method('translateString')
            ->with('OEEXAMPLESMODULE_API_HELLO')
            ->willReturn($expectedTranslation);

        $sut = $this->getSut(shopAdapter: $shopAdapterStub);

        $this->assertSame($expectedTranslation, $sut->getGreetingMessage());
    }

    private function getSut(
        ?ActiveProductCountDaoInterface $activeProductCountDao = null,
        ?ShopAdapterInterface $shopAdapter = null,
    ): ProductInfoService {
        return new ProductInfoService(
            activeProductCountDao: $activeProductCountDao
                ?? $this->createStub(ActiveProductCountDaoInterface::class),
            shopAdapter: $shopAdapter
                ?? $this->createStub(ShopAdapterInterface::class),
        );
    }
}
