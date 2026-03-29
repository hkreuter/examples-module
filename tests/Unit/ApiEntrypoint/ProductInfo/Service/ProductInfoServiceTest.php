<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Unit\ApiEntrypoint\ProductInfo\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\ExamplesModule\ApiEntrypoint\ProductInfo\Dao\ActiveProductCountDaoInterface;
use OxidEsales\ExamplesModule\ApiEntrypoint\ProductInfo\Service\ProductInfoService;
use OxidEsales\ExamplesModule\Core\Module as ModuleCore;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProductInfoService::class)]
final class ProductInfoServiceTest extends TestCase
{
    public function testGetActiveProductCountDelegatesToDao(): void
    {
        $expectedCount = mt_rand(1, 10000);

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
        $expectedTranslation = uniqid('translation_', true);

        $shopAdapterStub = $this->createStub(ShopAdapterInterface::class);
        $shopAdapterStub->method('translateString')
            ->with(ModuleCore::API_HELLO_LANGUAGE_CONST)
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
