<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Storefront\Subscriber;

use Fib\ProductLabel\Storefront\Page\ProductLabelPageDataLoader;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductLabelListingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProductLabelPageDataLoader $productLabelPageDataLoader,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductListingResultEvent::class => 'onProductListingLoaded',
        ];
    }

    public function onProductListingLoaded(ProductListingResultEvent $event): void
    {
        foreach ($event->getResult()->getEntities() as $product) {
            if (!$product instanceof SalesChannelProductEntity) {
                continue;
            }

            $labels = $this->productLabelPageDataLoader->load($product);
            $product->addExtension('productLabels', $labels);
        }
    }
}
