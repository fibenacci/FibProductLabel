<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel;

use Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\Log\Package;

#[Package('framework')]
class ProductLabelDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'fib_product_label';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductLabelEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductLabelCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new TranslatedField('name'))->addFlags(
                new ApiAware(),
                new Required(),
                new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)
            ),
            (new StringField('color', 'color', 16))->addFlags(new ApiAware(), new Required()),
            (new IntField('priority', 'priority'))->addFlags(new ApiAware()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new DateTimeField('valid_from', 'validFrom'))->addFlags(new ApiAware()),
            (new DateTimeField('valid_to', 'validTo'))->addFlags(new ApiAware()),

            (new TranslationsAssociationField(
                ProductLabelTranslationDefinition::class,
                'fib_product_label_id')
            )->addFlags(new ApiAware(), new Required()),
            (new ManyToManyAssociationField(
                'products',
                ProductDefinition::class,
                ProductLabelProductDefinition::class,
                'fib_product_label_id',
                'product_id'
            ))->addFlags(new ApiAware()),
            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}
