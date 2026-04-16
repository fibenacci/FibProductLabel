<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Unit\Storefront\Subscriber;

use Fib\ProductLabel\Core\Content\ProductLabel\Service\ProductLabelVisibilityService;
use Fib\ProductLabel\Storefront\Subscriber\ProductLabelCriteriaSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;

#[CoversClass(ProductLabelCriteriaSubscriber::class)]
class ProductLabelCriteriaSubscriberTest extends TestCase
{
    private ProductLabelVisibilityService&MockObject $visibilityService;
    private ProductLabelCriteriaSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->visibilityService = $this->createMock(ProductLabelVisibilityService::class);
        $this->subscriber        = new ProductLabelCriteriaSubscriber($this->visibilityService);
    }

    public function testOnCriteriaRequestCallsVisibilityService(): void
    {
        $criteria = new Criteria();
        $context  = $this->createMock(SalesChannelContext::class);
        $event    = $this->createMock(ProductPageCriteriaEvent::class);
        $event->method('getCriteria')->willReturn($criteria);
        $event->method('getSalesChannelContext')->willReturn($context);

        $this->visibilityService->expects(static::once())
            ->method('prepareCriteria')
            ->with($criteria);

        $this->subscriber->onCriteriaRequest($event);
    }

    public function testOnCriteriaRequestWorksForProductListingCriteriaEvent(): void
    {
        $criteria = new Criteria();
        $context  = $this->createMock(SalesChannelContext::class);
        $event    = $this->createMock(ProductListingCriteriaEvent::class);
        $event->method('getCriteria')->willReturn($criteria);
        $event->method('getSalesChannelContext')->willReturn($context);

        $this->visibilityService->expects(static::once())
            ->method('prepareCriteria')
            ->with($criteria);

        $this->subscriber->onCriteriaRequest($event);
    }

    public function testGetSubscribedEventsContainsAllRequiredEventTypes(): void
    {
        $events = ProductLabelCriteriaSubscriber::getSubscribedEvents();

        static::assertArrayHasKey(ProductPageCriteriaEvent::class, $events);
        static::assertArrayHasKey(ProductListingCriteriaEvent::class, $events);
        static::assertSame('onCriteriaRequest', $events[ProductPageCriteriaEvent::class]);
    }
}
