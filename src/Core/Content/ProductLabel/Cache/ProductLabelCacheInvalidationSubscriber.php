<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Cache;

use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductLabelCacheInvalidationSubscriber implements EventSubscriberInterface
{
    private const LABEL_ENTITY   = 'product_label';
    private const MAPPING_ENTITY = 'product_product_label';

    /**
     * @param EntityRepository<EntityCollection<Entity>> $mappingRepository
     */
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly EntityRepository $mappingRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityWrittenContainerEvent::class => 'invalidate',
        ];
    }

    public function invalidate(EntityWrittenContainerEvent $event): void
    {
        $tags = [];

        $changedLabelIds = $event->getPrimaryKeys(self::LABEL_ENTITY);

        foreach ($changedLabelIds as $labelId) {
            $tags[] = 'product-label-' . $labelId;
        }

        if ($changedLabelIds !== []) {
            $tags = [
                ...$tags,
                ...$this->resolveProductRouteTagsByLabelIds($changedLabelIds, $event->getContext()),
            ];
        }

        $mappingEvent = $event->getEventByEntityName(self::MAPPING_ENTITY);

        if ($mappingEvent instanceof EntityWrittenEvent) {
            /** @var EntityWriteResult<string> $writeResult */
            foreach ($mappingEvent->getWriteResults() as $writeResult) {
                $payload = $writeResult->getPayload();

                $productId      = $payload['productId'] ?? null;
                $productLabelId = $payload['productLabelId'] ?? null;

                if (is_string($productId)) {
                    $tags[] = 'product-label-route-' . $productId;
                }

                if (is_string($productLabelId)) {
                    $tags[] = 'product-label-' . $productLabelId;
                }
            }
        }

        /** @var list<string> $tags */
        $tags = array_values(array_unique($tags));

        if ($tags === []) {
            return;
        }

        $this->cacheInvalidator->invalidate($tags);
    }

    /**
     * @param list<string> $labelIds
     *
     * @return list<string>
     */
    private function resolveProductRouteTagsByLabelIds(array $labelIds, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('productLabelId', $labelIds));

        $mappingIds = $this->mappingRepository->search($criteria, $context);

        $tags = [];

        foreach ($mappingIds->getEntities() as $mappingEntity) {
            $productId = $mappingEntity->get('productId');

            if (is_string($productId)) {
                $tags[] = 'product-label-route-' . $productId;
            }
        }

        return array_values(array_unique($tags));
    }
}
