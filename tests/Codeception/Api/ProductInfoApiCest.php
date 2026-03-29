<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Codeception\Api;

use Codeception\Attribute\Group;
use OxidEsales\ExamplesModule\Tests\Codeception\Support\ApiTester;

#[Group('oe_examples_module')]
#[Group('oe_examples_module_api')]
final class ProductInfoApiCest
{
    public function testProductInfoReturnsJsonWithProductCount(ApiTester $I): void
    {
        $I->wantToTest('public product-info endpoint returns JSON');

        $I->sendGet('/api/product-info');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['message' => true]);
        $I->seeResponseJsonMatchesJsonPath('$.productCount');
        $I->seeResponseJsonMatchesJsonPath('$.message');
    }

    public function testProductInfoProductCountIsInteger(ApiTester $I): void
    {
        $I->wantToTest('product count is a positive integer');

        $I->sendGet('/api/product-info');

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);
        $I->assertIsInt($response['productCount']);
        $I->assertGreaterThan(0, $response['productCount']);
    }

    public function testProductInfoDoesNotRequireAuthentication(ApiTester $I): void
    {
        $I->wantToTest('product-info is accessible without authentication');

        $I->sendGet('/api/product-info');

        $I->seeResponseCodeIs(200);
    }
}
