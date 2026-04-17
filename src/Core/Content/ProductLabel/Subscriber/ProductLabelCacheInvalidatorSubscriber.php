<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Subscriber;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelTranslationDefinition;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingRoute;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductLabelCacheInvalidatorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly Connection $connection,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Use a high priority to ensure cache invalidation happens before other subscribers
            // that might rely on the updated state.
            EntityWrittenContainerEvent::class => [
                ['onEntityWritten', 2000],
            ],
        ];
    }

    public function onEntityWritten(EntityWrittenContainerEvent $event): void
    {
        $productIds = $this->getAffectedProductIds($event);

        if (empty($productIds)) {
            return;
        }

        $this->cacheInvalidator->invalidate($this->getTags($productIds));
    }

    /**
     * Get tags for products and their associated categories to ensure listings are updated.
     *
     * @param list<string> $productIds
     *
     * @return list<string>
     */
    private function getTags(array $productIds): array
    {
        return [
            ...array_map(EntityCacheKeyGenerator::buildProductTag(...), $productIds),
            ...array_map(ProductListingRoute::buildName(...), $this->getProductCategoryIds($productIds)),
        ];
    }

    /**
     * Resolve product IDs from written labels or mappings.
     * We use direct DBAL queries here to avoid the overhead of the DAL during cache invalidation.
     *
     * @return list<string>
     */
    private function getAffectedProductIds(EntityWrittenContainerEvent $event): array
    {
        $labelIds = [
            ...$event->getPrimaryKeys(ProductLabelDefinition::ENTITY_NAME),
            ...array_column($event->getPrimaryKeys(ProductLabelTranslationDefinition::ENTITY_NAME), 'productLabelId'),
        ];

        $productIds = array_column($event->getPrimaryKeys(ProductLabelProductDefinition::ENTITY_NAME), 'productId');

        if ($labelIds !== []) {
            $productIds = [...$productIds, ...$this->getProductIdsByLabelIds($labelIds)];
        }

        return array_values(array_unique($productIds));
    }

    /**
     * @param list<string> $labelIds
     *
     * @return list<string>
     */
    private function getProductIdsByLabelIds(array $labelIds): array
    {
        /** @var list<string> $ids */
        $ids = $this->connection->fetchFirstColumn(<<<SQL
            SELECT DISTINCT LOWER(HEX(`product_id`)) 
                       FROM `fib_product_label_product` 
                      WHERE `fib_product_label_id` 
                         IN (:ids)
            SQL,
            ['ids' => Uuid::fromHexToBytesList($labelIds)],
            ['ids' => ArrayParameterType::STRING]
        );

        return $ids;
    }

    /**
     * @param list<string> $productIds
     *
     * @return list<string>
     */
    private function getProductCategoryIds(array $productIds): array
    {
        /** @var list<string> $ids */
        $ids = $this->connection->fetchFirstColumn(<<<SQL
            SELECT DISTINCT LOWER(HEX(category_id))
                       FROM product_category_tree
                      WHERE product_id IN (:ids)
            SQL,
            ['ids' => Uuid::fromHexToBytesList($productIds)],
            ['ids' => ArrayParameterType::STRING]
        );

        return $ids;
    }
}
