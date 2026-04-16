<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Extension;

use Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductLabelProductExtension extends EntityExtension
{
    public function getEntityName(): string
    {
        return ProductDefinition::ENTITY_NAME;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $field = (new ManyToManyAssociationField(
            'fibProductLabels',
            ProductLabelDefinition::class,
            ProductLabelProductDefinition::class,
            'product_id',
            'fib_product_label_id'
        ))->addFlags(new ApiAware());

        $collection->add($field);
    }
}
