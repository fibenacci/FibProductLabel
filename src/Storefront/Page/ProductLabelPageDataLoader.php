<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Storefront\Page;

use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;
use Fib\ProductLabel\Storefront\Struct\ProductLabel\ProductLabelCollectionStruct;
use Fib\ProductLabel\Storefront\Struct\ProductLabel\ProductLabelStruct;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Adapter\Cache\CacheTagCollector;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

final class ProductLabelPageDataLoader
{
    public function __construct(
        private readonly CacheTagCollector $cacheTagCollector,
    ) {
    }

    public function load(ProductEntity $product): ProductLabelCollectionStruct
    {
        $collection = new ProductLabelCollectionStruct();

        $labels = $product->getExtension('productLabels');

        if (!$labels instanceof EntityCollection) {
            return $collection;
        }

        $labelIds = [];

        /** @var ProductLabelEntity $label */
        foreach ($labels as $label) {
            $labelIds[] = $label->getId();

            $name     = $label->getTranslation('name');
            $color    = $label->getColor();
            $priority = $label->getPriority();

            if (!empty($name) || !empty($color) || !empty($priority)) {
                continue;
            }

            $collection->add(new ProductLabelStruct(
                $label->getId(),
                $name,
                $color,
                $priority,
            ));
        }

        $this->cacheTagCollector->addTag('product-label-route-' . $product->getId());

        foreach ($labelIds as $labelId) {
            $this->cacheTagCollector->addTag('product-label-' . $labelId);
        }

        return $collection;
    }
}
