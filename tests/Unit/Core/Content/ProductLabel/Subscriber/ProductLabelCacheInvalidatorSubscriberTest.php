<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Unit\Core\Content\ProductLabel\Subscriber;

use Doctrine\DBAL\Connection;
use Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\Subscriber\ProductLabelCacheInvalidatorSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\Uuid\Uuid;

#[CoversClass(ProductLabelCacheInvalidatorSubscriber::class)]
class ProductLabelCacheInvalidatorSubscriberTest extends TestCase
{
    // -------------------------------------------------------------------------
    // US-05: Cache invalidation
    // -------------------------------------------------------------------------

    public function testOnEntityWrittenDoesNothingForUnrelatedEntities(): void
    {
        $cacheInvalidator = $this->createMock(CacheInvalidator::class);
        $cacheInvalidator->expects(static::never())->method('invalidate');

        $connection = $this->createMock(Connection::class);

        $subscriber = new ProductLabelCacheInvalidatorSubscriber($cacheInvalidator, $connection);

        $event = $this->makeContainerEventWithEntityName('unrelated_entity', [Uuid::randomHex()]);

        $subscriber->onEntityWritten($event);
    }

    public function testOnEntityWrittenInvalidatesTagsWhenProductLabelProductIsWritten(): void
    {
        $productId = Uuid::randomHex();

        $cacheInvalidator = $this->createMock(CacheInvalidator::class);
        $cacheInvalidator
            ->expects(static::once())
            ->method('invalidate')
            ->with(static::callback(static function (array $tags) use ($productId): bool {
                foreach ($tags as $tag) {
                    if (str_contains($tag, $productId)) {
                        return true;
                    }
                }

                return false;
            }));

        $connection = $this->createMock(Connection::class);
        $connection->method('fetchFirstColumn')->willReturn([]);

        $subscriber = new ProductLabelCacheInvalidatorSubscriber($cacheInvalidator, $connection);

        $event = $this->makeContainerEventWithEntityName(
            ProductLabelProductDefinition::ENTITY_NAME,
            [['productId' => $productId, 'productLabelId' => Uuid::randomHex(), 'productVersionId' => Uuid::randomHex()]],
        );

        $subscriber->onEntityWritten($event);
    }

    public function testGetSubscribedEventsListensToEntityWrittenContainerEvent(): void
    {
        $events = ProductLabelCacheInvalidatorSubscriber::getSubscribedEvents();

        static::assertArrayHasKey(EntityWrittenContainerEvent::class, $events);
    }

    /**
     * @param list<array<string, mixed>>|list<string> $primaryKeys
     */
    private function makeContainerEventWithEntityName(string $entityName, array $primaryKeys): EntityWrittenContainerEvent
    {
        $event = $this->createMock(EntityWrittenContainerEvent::class);

        $event->method('getPrimaryKeys')
            ->willReturnCallback(static function (string $name) use ($entityName, $primaryKeys): array {
                return $name === $entityName ? $primaryKeys : [];
            });

        return $event;
    }
}
