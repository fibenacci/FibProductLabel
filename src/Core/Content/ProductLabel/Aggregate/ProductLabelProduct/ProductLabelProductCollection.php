<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<ProductLabelProductEntity>
 */
#[Package('framework')]
class ProductLabelProductCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductLabelProductEntity::class;
    }
}
