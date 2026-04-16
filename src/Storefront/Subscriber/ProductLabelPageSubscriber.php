<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Storefront\Subscriber;

use Fib\ProductLabel\Storefront\Page\ProductLabelPageDataLoader;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductLabelPageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProductLabelPageDataLoader $productLabelPageDataLoader,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();
        $labels  = $this->productLabelPageDataLoader->load($product);

        $event->getPage()->addExtension('productLabels', $labels);
    }
}
