<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Integration\Core\Content\ProductLabel;

use Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductCollection;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelCollection;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Tax\TaxEntity;
use Shopware\Core\Test\TestDefaults;

/**
 * Tests US-02: Labels can be assigned to products via the M2M relation.
 */
class ProductLabelProductAssignmentTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testLabelCanBeAssignedToProduct(): void
    {
        $context = Context::createDefaultContext();
        /** @var EntityRepository<ProductLabelCollection> $labelRepo */
        $labelRepo = static::getContainer()->get('fib_product_label.repository');

        $labelId   = Uuid::randomHex();
        $productId = $this->createProduct($context);

        $labelRepo->create([[
            'id'           => $labelId,
            'color'        => '#FF0000',
            'priority'     => 10,
            'active'       => true,
            'translations' => [
                Defaults::LANGUAGE_SYSTEM => ['name' => 'Hot Deal'],
            ],
            'products' => [['id' => $productId, 'versionId' => Defaults::LIVE_VERSION]],
        ]], $context);

        $criteria = new Criteria([$labelId]);
        $criteria->addAssociation('products');

        $label = $labelRepo->search($criteria, $context)->first();
        static::assertInstanceOf(ProductLabelEntity::class, $label);

        static::assertNotNull($label->getProducts());
        static::assertSame(1, $label->getProducts()->count());
        static::assertNotNull($label->getProducts()->get($productId));
    }

    public function testRemovingAssignmentDeletesMappingRow(): void
    {
        $context = Context::createDefaultContext();
        /** @var EntityRepository<ProductLabelCollection> $labelRepo */
        $labelRepo = static::getContainer()->get('fib_product_label.repository');

        $labelId   = Uuid::randomHex();
        $productId = $this->createProduct($context);

        $labelRepo->create([[
            'id'           => $labelId,
            'color'        => '#00FF00',
            'priority'     => 5,
            'active'       => true,
            'translations' => [
                Defaults::LANGUAGE_SYSTEM => ['name' => 'New'],
            ],
            'products' => [['id' => $productId, 'versionId' => Defaults::LIVE_VERSION]],
        ]], $context);

        // Remove the product assignment explicitly via mapping repository
        /** @var EntityRepository<ProductLabelProductCollection> $mappingRepo */
        $mappingRepo = static::getContainer()->get('fib_product_label_product.repository');
        $mappingRepo->delete([[
            'productLabelId' => $labelId,
            'productId'      => $productId,
        ]], $context);

        $criteria = new Criteria([$labelId]);
        $criteria->addAssociation('products');

        $label = $labelRepo->search($criteria, $context)->first();
        static::assertInstanceOf(ProductLabelEntity::class, $label);

        static::assertInstanceOf(ProductCollection::class, $label->getProducts());
        static::assertSame(0, $label->getProducts()->count());
    }

    public function testProductExposesLabelsViaExtension(): void
    {
        $context = Context::createDefaultContext();
        /** @var EntityRepository<ProductLabelCollection> $labelRepo */
        $labelRepo = static::getContainer()->get('fib_product_label.repository');
        /** @var EntityRepository<ProductCollection> $productRepo */
        $productRepo = static::getContainer()->get('product.repository');

        $labelId   = Uuid::randomHex();
        $productId = $this->createProduct($context);

        $labelRepo->create([[
            'id'           => $labelId,
            'color'        => '#0000FF',
            'priority'     => 20,
            'active'       => true,
            'translations' => [
                Defaults::LANGUAGE_SYSTEM => ['name' => 'Featured'],
            ],
            'products' => [['id' => $productId, 'versionId' => Defaults::LIVE_VERSION]],
        ]], $context);

        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('fibProductLabels');

        $product = $productRepo->search($criteria, $context)->first();
        static::assertInstanceOf(ProductEntity::class, $product);

        $labels = $product->getExtension('fibProductLabels');

        static::assertInstanceOf(ProductLabelCollection::class, $labels);
        static::assertSame(1, $labels->count());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createProduct(Context $context): string
    {
        /** @var EntityRepository<ProductCollection> $productRepo */
        $productRepo = static::getContainer()->get('product.repository');
        $productId   = Uuid::randomHex();
        $taxId       = $this->getValidTaxId($context);

        $productRepo->create([[
            'id'            => $productId,
            'productNumber' => 'TEST-' . $productId,
            'stock'         => 10,
            'name'          => 'Test Product',
            'price'         => [['currencyId' => Defaults::CURRENCY, 'gross' => 10, 'net' => 8.40, 'linked' => false]],
            'taxId'         => $taxId,
            'visibilities'  => [[
                'salesChannelId' => TestDefaults::SALES_CHANNEL,
                'visibility'     => 30,
            ]],
        ]], $context);

        return $productId;
    }

    private function getValidTaxId(Context $context): string
    {
        /** @var EntityRepository<EntityCollection<TaxEntity>> $taxRepo */
        $taxRepo = static::getContainer()->get('tax.repository');
        $tax     = $taxRepo->search(new Criteria(), $context)->first();
        static::assertInstanceOf(TaxEntity::class, $tax);

        return $tax->getId();
    }
}
