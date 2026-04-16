<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Storefront\Struct\ProductLabel;

use Shopware\Core\Framework\Struct\Collection;

/**
 * @extends Collection<ProductLabelStruct>
 */
final class ProductLabelCollectionStruct extends Collection
{
    public function getApiAlias(): string
    {
        return 'swag_product_label_collection';
    }

    protected function getExpectedClass(): string
    {
        return ProductLabelStruct::class;
    }
}
