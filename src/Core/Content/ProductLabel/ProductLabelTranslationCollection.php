<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<ProductLabelTranslationEntity>
 */
#[Package('framework')]
class ProductLabelTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductLabelTranslationEntity::class;
    }
}
