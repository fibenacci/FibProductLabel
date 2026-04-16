<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\Language\LanguageDefinition;

#[Package('framework')]
class ProductLabelTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'fib_product_label_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ProductLabelTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return ProductLabelTranslationEntity::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return ProductLabelDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField(
                'fib_product_label_id',
                'productLabelId',
                ProductLabelDefinition::class
            ))->addFlags(new PrimaryKey(), new Required()),
            (new FkField(
                'language_id',
                'languageId',
                LanguageDefinition::class
            ))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new ApiAware(), new Required()),
            new ManyToOneAssociationField(
                'productLabel',
                'fib_product_label_id',
                ProductLabelDefinition::class,
                'id',
                false
            ),
            new ManyToOneAssociationField('language', 'language_id', LanguageDefinition::class, 'id', false),
            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}
