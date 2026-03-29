<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Codeception\Support;

use Codeception\Util\Fixtures;
use OxidEsales\Facts\Facts;

final class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    public function getShopUrl(): string
    {
        return (new Facts())->getShopUrl();
    }
}
