<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Storefront\Page;

use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;
use Fib\ProductLabel\Storefront\Struct\ProductLabel\ProductLabelCollectionStruct;
use Fib\ProductLabel\Storefront\Struct\ProductLabel\ProductLabelStruct;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Adapter\Cache\CacheTagCollector;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * This service converts the DAL ProductLabelEntity objects into lean Struct objects
 * for the storefront and handles granular cache tagging.
 */
final class ProductLabelPageDataLoader
{
    public function __construct(
        private readonly CacheTagCollector $cacheTagCollector,
    ) {
    }

    public function load(ProductEntity $product): ProductLabelCollectionStruct
    {
        $collection = new ProductLabelCollectionStruct();

        // Get the labels assigned to the product via the Extension.
        // The association name 'fibProductLabels' is defined in ProductLabelProductExtension.
        $labels = $product->getExtension('fibProductLabels');

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

            // Skip labels that have no content to display
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

        // Add a specific tag for this product's labels to allow precise cache invalidation
        $this->cacheTagCollector->addTag('product-label-route-' . $product->getId());

        // Also add tags for each individual label to invalidate the product cache if a label changes
        foreach ($labelIds as $labelId) {
            $this->cacheTagCollector->addTag('product-label-' . $labelId);
        }

        return $collection;
    }

    /**
     * Helper to safely extract translated strings from the 'translated' array.
     *
     * @param array<string, mixed> $translated
     */
    private function getTranslatedString(array $translated, string $key, string $default = ''): string
    {
        $value = $translated[$key] ?? null;

        return is_string($value) ? $value : $default;
    }
}
