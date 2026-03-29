<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Step\Start as StartStep;
use OxidEsales\ExamplesModule\Tests\Codeception\Support\AcceptanceTester;

#[Group('oe_examples_module')]
#[Group('oe_examples_module_api')]
final class UserInfoGreetingButtonCest
{
    public function testGreetingButtonNotVisibleForAnonymousUser(
        AcceptanceTester $I
    ): void {
        $I->wantToTest(
            'greeting button is hidden for anonymous users'
        );

        $I->openShop();
        $I->waitForPageLoad();

        $I->dontSeeElement('#oeem-greeting-btn');
    }

    public function testGreetingButtonShowsFirstNameForLoggedInUser(
        AcceptanceTester $I
    ): void {
        $I->wantToTest(
            'greeting button shows user first name when logged in'
        );

        $startStep = new StartStep($I);
        $startStep->loginOnStartPage(
            $I->getDemoUserName(),
            $I->getDemoUserPassword()
        );

        $I->waitForPageLoad();
        $I->waitForElementVisible('#oeem-greeting-btn', 5);
        $I->seeElement('#oeem-greeting-btn');

        $buttonText = $I->grabTextFrom('#oeem-greeting-btn');
        $I->assertNotEmpty($buttonText);
    }

    public function testGreetingButtonLinksToGreetingController(
        AcceptanceTester $I
    ): void {
        $I->wantToTest(
            'greeting button links to the greeting controller'
        );

        $startStep = new StartStep($I);
        $startStep->loginOnStartPage(
            $I->getDemoUserName(),
            $I->getDemoUserPassword()
        );

        $I->waitForPageLoad();
        $I->waitForElementVisible('#oeem-greeting-btn', 5);
        $I->click('#oeem-greeting-btn');
        $I->waitForPageLoad();

        $I->seeInCurrentUrl('oeem_greeting');
    }
}
