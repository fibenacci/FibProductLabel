<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Unit\Core\Content\ProductLabel\Cache;

use Fib\ProductLabel\Core\Content\ProductLabel\Cache\ProductLabelCacheInvalidationSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteResult;
use Shopware\Core\Framework\Uuid\Uuid;

final class ProductLabelCacheInvalidationSubscriberTest extends TestCase
{
    private CacheInvalidator&MockObject $cacheInvalidator;

    /**
     * @var EntityRepository<EntityCollection<\Shopware\Core\Framework\DataAbstractionLayer\Entity>>&MockObject
     */
    private EntityRepository&MockObject $mappingRepository;

    protected function setUp(): void
    {
        $this->cacheInvalidator  = $this->createMock(CacheInvalidator::class);
        $this->mappingRepository = $this->createMock(EntityRepository::class);
    }

    public function testInvalidatesLabelAndProductRouteTagsWhenLabelChanges(): void
    {
        $labelId   = Uuid::randomHex();
        $productId = Uuid::randomHex();
        $context   = Context::createDefaultContext();

        $mappingEntity = new class($productId) extends \Shopware\Core\Framework\DataAbstractionLayer\Entity {
            public function __construct(private readonly string $productId)
            {
            }

            public function get(string $property): mixed
            {
                if ($property === 'productId') {
                    return $this->productId;
                }

                return null;
            }
        };

        $searchResult = new EntitySearchResult(
            'product_product_label',
            1,
            new EntityCollection([$mappingEntity]),
            null,
            new Criteria(),
            $context
        );

        $this->mappingRepository
            ->expects(static::once())
            ->method('search')
            ->willReturn($searchResult);

        $event = $this->createMock(EntityWrittenContainerEvent::class);
        $event->method('getPrimaryKeys')
            ->willReturnCallback(static function (string $entityName) use ($labelId): array {
                if ($entityName === 'product_label') {
                    return [$labelId];
                }

                return [];
            });
        $event->method('getEventByEntityName')->willReturn(null);
        $event->method('getContext')->willReturn($context);

        $this->cacheInvalidator
            ->expects(static::once())
            ->method('invalidate')
            ->with(static::callback(static function (array $tags) use ($labelId, $productId): bool {
                sort($tags);

                $expected = [
                    'product-label-' . $labelId,
                    'product-label-route-' . $productId,
                ];
                sort($expected);

                return $tags === $expected;
            }));

        $subscriber = new ProductLabelCacheInvalidationSubscriber(
            $this->cacheInvalidator,
            $this->mappingRepository
        );

        $subscriber->invalidate($event);
    }

    public function testInvalidatesTagsWhenMappingChanges(): void
    {
        $labelId   = Uuid::randomHex();
        $productId = Uuid::randomHex();
        $context   = Context::createDefaultContext();

        $writeResult = $this->createMock(WriteResult::class);
        $writeResult->method('getPayload')->willReturn([
            'productId'      => $productId,
            'productLabelId' => $labelId,
        ]);

        $mappingEvent = $this->createMock(EntityWrittenEvent::class);
        $mappingEvent->method('getWriteResults')->willReturn([$writeResult]);

        $event = $this->createMock(EntityWrittenContainerEvent::class);
        $event->method('getPrimaryKeys')->willReturn([]);
        $event->method('getEventByEntityName')
            ->willReturnCallback(static function (string $entityName) use ($mappingEvent) {
                if ($entityName === 'product_product_label') {
                    return $mappingEvent;
                }

                return null;
            });
        $event->method('getContext')->willReturn($context);

        $this->mappingRepository
            ->expects(static::never())
            ->method('search');

        $this->cacheInvalidator
            ->expects(static::once())
            ->method('invalidate')
            ->with(static::callback(static function (array $tags) use ($labelId, $productId): bool {
                sort($tags);

                $expected = [
                    'product-label-' . $labelId,
                    'product-label-route-' . $productId,
                ];
                sort($expected);

                return $tags === $expected;
            }));

        $subscriber = new ProductLabelCacheInvalidationSubscriber(
            $this->cacheInvalidator,
            $this->mappingRepository
        );

        $subscriber->invalidate($event);
    }

    public function testDoesNothingWhenNoRelevantChangesExist(): void
    {
        $context = Context::createDefaultContext();

        $event = $this->createMock(EntityWrittenContainerEvent::class);
        $event->method('getPrimaryKeys')->willReturn([]);
        $event->method('getEventByEntityName')->willReturn(null);
        $event->method('getContext')->willReturn($context);

        $this->mappingRepository
            ->expects(static::never())
            ->method('search');

        $this->cacheInvalidator
            ->expects(static::never())
            ->method('invalidate');

        $subscriber = new ProductLabelCacheInvalidationSubscriber(
            $this->cacheInvalidator,
            $this->mappingRepository
        );

        $subscriber->invalidate($event);
    }
}
