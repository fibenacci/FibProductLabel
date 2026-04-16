<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Storefront\Subscriber;

use Fib\ProductLabel\Core\Content\ProductLabel\Service\ProductLabelVisibilityService;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductLabelCriteriaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProductLabelVisibilityService $productLabelVisibilityService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageCriteriaEvent::class    => 'onCriteriaRequest',
            ProductListingCriteriaEvent::class => 'onCriteriaRequest',
            ProductSearchCriteriaEvent::class  => 'onCriteriaRequest',
        ];
    }

    public function onCriteriaRequest(
        ProductPageCriteriaEvent|ProductListingCriteriaEvent|ProductSearchCriteriaEvent $event,
    ): void {
        $this->productLabelVisibilityService->prepareCriteria(
            $event->getCriteria()
        );
    }
}
