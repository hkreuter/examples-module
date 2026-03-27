<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\ProductInfo\Dao;

interface ActiveProductCountDaoInterface
{
    public function getActiveProductCount(): int;
}
