<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct;

use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\Framework\Log\Package;

#[Package('framework')]
class ProductLabelProductDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 'fib_product_label_product';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function isVersionAware(): bool
    {
        return true;
    }

    public function getCollectionClass(): string
    {
        return ProductLabelProductCollection::class;
    }

    public function getEntityClass(): string
    {
        return ProductLabelProductEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField(
                'fib_product_label_id',
                'productLabelId',
                ProductLabelDefinition::class
            ))->addFlags(
                new PrimaryKey(),
                new Required()
            ),
            (new FkField(
                'product_id',
                'productId',
                ProductDefinition::class
            ))->addFlags(new PrimaryKey(), new Required()),
            (new ReferenceVersionField(ProductDefinition::class))->addFlags(
                new PrimaryKey(),
                new Required()
            ),
            new ManyToOneAssociationField(
                'productLabel',
                'fib_product_label_id',
                ProductLabelDefinition::class,
                'id',
                false
            ),
            new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class,
                'id',
                false
            ),
            new CreatedAtField(),
        ]);
    }
}
