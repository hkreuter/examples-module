<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\ExamplesModule\Tests\Integration\ApiEntrypoint\ProductInfo\Dao;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\ExamplesModule\ApiEntrypoint\ProductInfo\Dao\ActiveProductCountDao;
use OxidEsales\ExamplesModule\ApiEntrypoint\ProductInfo\Dao\ActiveProductCountDaoInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ActiveProductCountDao::class)]
final class ActiveProductCountDaoTest extends IntegrationTestCase
{
    #[Test]
    public function countReturnsZeroWhenNoActiveProducts(): void
    {
        $this->deleteTableContent('oxarticles');

        $sut = $this->get(ActiveProductCountDaoInterface::class);

        $this->assertSame(0, $sut->getActiveProductCount());
    }

    #[Test]
    public function countReturnsOnlyActiveParentProducts(): void
    {
        $this->deleteTableContent('oxarticles');

        $parentId = '_tart' . substr(uniqid('', true), 0, 22);
        $this->createArticle(id: $parentId, active: true);
        $this->createArticle(
            id: '_tart' . substr(uniqid('', true), 0, 22),
            active: true,
            parentId: $parentId,
        );
        $this->createArticle(
            id: '_tart' . substr(uniqid('', true), 0, 22),
            active: false,
        );

        $sut = $this->get(ActiveProductCountDaoInterface::class);

        $this->assertSame(1, $sut->getActiveProductCount());
    }

    #[Test]
    public function countReflectsMultipleActiveProducts(): void
    {
        $this->deleteTableContent('oxarticles');

        $count = mt_rand(2, 5);
        for ($i = 0; $i < $count; $i++) {
            $this->createArticle(
                id: '_tart' . substr(uniqid('', true), 0, 22),
                active: true,
            );
        }

        $sut = $this->get(ActiveProductCountDaoInterface::class);

        $this->assertSame($count, $sut->getActiveProductCount());
    }

    private function createArticle(
        string $id,
        bool $active,
        string $parentId = '',
    ): void {
        $article = oxNew(Article::class);
        $article->setId($id);
        $article->assign([
            'oxactive' => (int) $active,
            'oxtitle' => uniqid('title_', true),
            'oxparentid' => $parentId,
            'oxartnum' => 'TEST-' . substr($id, -8),
            'oxshopid' => 1,
        ]);
        $article->save();
    }

    private function deleteTableContent(string $table): void
    {
        $this->get(\OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface::class)
            ->create()
            ->delete($table)
            ->execute();
    }
}
