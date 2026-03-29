<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Codeception\Api;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\ExamplesModule\Tests\Codeception\Support\ApiTester;

#[Group('oe_examples_module')]
#[Group('oe_examples_module_api')]
final class CustomerGroupApiCest
{
    public function testCustomerGroupsRequiresAuthentication(ApiTester $I): void
    {
        $I->wantToTest('customer-groups endpoint rejects unauthenticated requests');

        $I->sendGet('/api/customer-groups');

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }

    public function testCustomerGroupsReturnsDataWithAdminToken(ApiTester $I): void
    {
        $I->wantToTest('customer-groups endpoint returns data for admin');

        $token = $this->loginAsAdmin($I);

        $I->amBearerAuthenticated($token);
        $I->sendGet('/api/customer-groups');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.customerGroups');
        $I->seeResponseJsonMatchesJsonPath('$.total');
    }

    public function testCustomerGroupsContainsGroupStructure(ApiTester $I): void
    {
        $I->wantToTest('customer group entries have correct structure');

        $token = $this->loginAsAdmin($I);

        $I->amBearerAuthenticated($token);
        $I->sendGet('/api/customer-groups');

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        $I->assertNotEmpty($response['customerGroups']);
        $I->assertArrayHasKey('groupId', $response['customerGroups'][0]);
        $I->assertArrayHasKey('title', $response['customerGroups'][0]);
        $I->assertArrayHasKey('count', $response['customerGroups'][0]);
    }

    public function testCustomerGroupsRejectsFrontendUserToken(ApiTester $I): void
    {
        $I->wantToTest('customer-groups rejects non-admin user');

        $token = $this->loginAsUser($I);

        $I->amBearerAuthenticated($token);
        $I->sendGet('/api/customer-groups');

        $I->seeResponseCodeIs(403);
    }

    private function loginAsAdmin(ApiTester $I): string
    {
        $admin = Fixtures::get('adminUser');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/api/login', [
            'username' => $admin['email'],
            'password' => $admin['password'],
        ]);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        return $response['body']['token'];
    }

    private function loginAsUser(ApiTester $I): string
    {
        $user = Fixtures::get('user');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/api/login', [
            'username' => $user['email'],
            'password' => $user['password'],
        ]);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        return $response['body']['token'];
    }
}
