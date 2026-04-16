<?php declare(strict_types=1);

use Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\Cache\ProductLabelCacheInvalidationSubscriber;
use Fib\ProductLabel\Core\Content\ProductLabel\Commands\DeactivateExpiredLabelCommand;
use Fib\ProductLabel\Core\Content\ProductLabel\Extension\ProductLabelProductExtension;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelTranslationDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\ScheduledTask\DeactivateExpiredLabelsTask;
use Fib\ProductLabel\Core\Content\ProductLabel\ScheduledTask\DeactivateExpiredLabelsTaskHandler;
use Fib\ProductLabel\Core\Content\ProductLabel\Service\DeactivateExpiredLabelsService;
use Fib\ProductLabel\Core\Content\ProductLabel\Service\ProductLabelVisibilityService;
use Fib\ProductLabel\Storefront\Page\ProductLabelPageDataLoader;
use Fib\ProductLabel\Storefront\Subscriber\ProductLabelCriteriaSubscriber;
use Fib\ProductLabel\Storefront\Subscriber\ProductLabelListingSubscriber;
use Fib\ProductLabel\Storefront\Subscriber\ProductLabelPageSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure(false)
        ->public(false);

    $services->set(ProductLabelDefinition::class)
        ->tag('shopware.entity.definition', ['entity' => 'fib_product_label']);

    $services->set(ProductLabelTranslationDefinition::class)
        ->tag('shopware.entity.definition', ['entity' => 'fib_product_label_translation']);

    $services->set(ProductLabelProductDefinition::class)
        ->tag('shopware.entity.definition', ['entity' => 'fib_product_label_product']);

    $services->set(ProductLabelProductExtension::class)
        ->tag('shopware.entity.extension');

    $services->set(ProductLabelVisibilityService::class);

    $services->set(DeactivateExpiredLabelsService::class)
        ->arg('$productLabelRepository', service('fib_product_label.repository'));

    $services->set(DeactivateExpiredLabelCommand::class)
        ->tag('console.command');

    $services->set(ProductLabelCriteriaSubscriber::class)
        ->tag('kernel.event_subscriber');

    $services->set(DeactivateExpiredLabelsTask::class)
        ->tag('shopware.scheduled.task');

    $services->set(DeactivateExpiredLabelsTaskHandler::class)
        ->arg('$scheduledTaskRepository', service('scheduled_task.repository'))
        ->arg('$logger', service('logger'))
        ->tag('messenger.message_handler');

    $services->set(ProductLabelPageDataLoader::class);

    $services->set(ProductLabelPageSubscriber::class)
        ->tag('kernel.event_subscriber');

    $services->set(ProductLabelListingSubscriber::class)
        ->tag('kernel.event_subscriber');

    $services->set(ProductLabelCacheInvalidationSubscriber::class)
        ->arg('$mappingRepository', service('fib_product_label_product.repository'))
        ->tag('kernel.event_subscriber');
};