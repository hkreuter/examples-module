<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Unit\ProductInfo\Controller;

use OxidEsales\ExamplesModule\ProductInfo\Controller\ProductInfoApiController;
use OxidEsales\ExamplesModule\ProductInfo\Service\ProductInfoServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

#[CoversClass(ProductInfoApiController::class)]
final class ProductInfoApiControllerTest extends TestCase
{
    public function testGetProductInfoReturnsJsonResponse(): void
    {
        $sut = $this->getSut();

        $response = $sut->getProductInfo();

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testGetProductInfoReturnsStatus200(): void
    {
        $sut = $this->getSut();

        $response = $sut->getProductInfo();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetProductInfoContainsProductCount(): void
    {
        $expectedCount = 99;

        $serviceStub = $this->createStub(ProductInfoServiceInterface::class);
        $serviceStub->method('getActiveProductCount')
            ->willReturn($expectedCount);
        $serviceStub->method('getGreetingMessage')
            ->willReturn('');

        $sut = $this->getSut(productInfoService: $serviceStub);

        $data = $this->decodeResponse($sut->getProductInfo());

        $this->assertSame($expectedCount, $data['productCount']);
    }

    public function testGetProductInfoContainsTranslatedMessage(): void
    {
        $expectedMessage = 'Hallo von der OXID eShop API';

        $serviceStub = $this->createStub(ProductInfoServiceInterface::class);
        $serviceStub->method('getActiveProductCount')
            ->willReturn(0);
        $serviceStub->method('getGreetingMessage')
            ->willReturn($expectedMessage);

        $sut = $this->getSut(productInfoService: $serviceStub);

        $data = $this->decodeResponse($sut->getProductInfo());

        $this->assertSame($expectedMessage, $data['message']);
    }

    public function testGetProductInfoResponseStructure(): void
    {
        $serviceStub = $this->createStub(ProductInfoServiceInterface::class);
        $serviceStub->method('getActiveProductCount')->willReturn(5);
        $serviceStub->method('getGreetingMessage')->willReturn('Hello');

        $sut = $this->getSut(productInfoService: $serviceStub);

        $data = $this->decodeResponse($sut->getProductInfo());

        $this->assertArrayHasKey('productCount', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertCount(2, $data);
    }

    private function getSut(
        ?ProductInfoServiceInterface $productInfoService = null,
    ): ProductInfoApiController {
        return new ProductInfoApiController(
            productInfoService: $productInfoService
                ?? $this->createStub(ProductInfoServiceInterface::class),
        );
    }

    private function decodeResponse(JsonResponse $response): array
    {
        return json_decode($response->getContent(), true);
    }
}
