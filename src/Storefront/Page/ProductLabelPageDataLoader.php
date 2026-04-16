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

            $name     = $this->getTranslatedString($label->getTranslated(), 'name');
            $color    = $label->getColor();
            $priority = $label->getPriority();

            if ($name === '' && $color === '' && $priority === 0) {
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

    /**
     * Leave this here for the specific use-case and just prevent type narrow issue on mixed stuff above.
     * for now this kind of trade-off is fair enough.
     *
     * @param array<string, mixed> $translated
     */
    private function getTranslatedString(array $translated, string $key, string $default = ''): string
    {
        $value = $translated[$key] ?? null;

        return is_string($value) ? $value : $default;
    }
}
