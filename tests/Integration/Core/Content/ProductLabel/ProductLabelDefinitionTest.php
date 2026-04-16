<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Integration\Core\Content\ProductLabel;

use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelCollection;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

#[CoversClass(ProductLabelEntity::class)]
class ProductLabelDefinitionTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testProductLabelCanBeWrittenAndReadBack(): void
    {
        /** @var EntityRepository<ProductLabelCollection> $repository */
        $repository = static::getContainer()->get('fib_product_label.repository');
        $id         = Uuid::randomHex();
        $context    = Context::createDefaultContext();

        $repository->create([[
            'id'           => $id,
            'color'        => '#e11d48',
            'priority'     => 80,
            'active'       => true,
            'translations' => [
                Defaults::LANGUAGE_SYSTEM => ['name' => 'New Arrival'],
            ],
        ]], $context);

        $criteria = new Criteria([$id]);
        $criteria->addAssociation('translations');

        $label = $repository->search($criteria, $context)->first();

        static::assertInstanceOf(ProductLabelEntity::class, $label);
        static::assertSame('New Arrival', $label->getName());
    }

    public function testEntityCanBeCreated(): void
    {
        /** @var EntityRepository<ProductLabelCollection> $repository */
        $repository = static::getContainer()->get('fib_product_label.repository');
        $id         = Uuid::randomHex();
        $context    = Context::createDefaultContext();

        $repository->create([[
            'id'           => $id,
            'color'        => '#000000',
            'priority'     => 1,
            'active'       => true,
            'translations' => [
                Defaults::LANGUAGE_SYSTEM => ['name' => 'Test Label'],
            ],
        ]], $context);

        $criteria = new Criteria([$id]);
        $label    = $repository->search($criteria, $context)->first();

        static::assertInstanceOf(ProductLabelEntity::class, $label);
        static::assertSame('Test Label', $label->getName());
    }
}
