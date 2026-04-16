<?php declare(strict_types=1);

use Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\Commands\DeactivateExpiredLabelCommand;
use Fib\ProductLabel\Core\Content\ProductLabel\Extension\ProductLabelProductExtension;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelTranslationDefinition;
use Fib\ProductLabel\Core\Content\ProductLabel\Service\ProductLabelVisibilityService;
use Fib\ProductLabel\Core\Content\ProductLabel\ScheduledTask\DeactivateExpiredLabelsTask;
use Fib\ProductLabel\Core\Content\ProductLabel\ScheduledTask\DeactivateExpiredLabelsTaskHandler;
use Fib\ProductLabel\Core\Content\ProductLabel\Service\DeactivateExpiredLabelsService;
use Fib\ProductLabel\Storefront\Subscriber\ProductLabelCriteriaSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

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
        ->args([
            service('fib_product_label.repository'),
        ]);

    $services->set(DeactivateExpiredLabelCommand::class)
        ->args([
            service(DeactivateExpiredLabelsService::class),
        ])
        ->tag('console.command');

    $services->set(ProductLabelCriteriaSubscriber::class)
        ->args([
            service(ProductLabelVisibilityService::class),
        ])
        ->tag('kernel.event_subscriber');

    $services->set(DeactivateExpiredLabelsTask::class)
        ->tag('shopware.scheduled.task');

    $services->set(DeactivateExpiredLabelsTaskHandler::class)
        ->args([
            service('scheduled_task.repository'),
            service('logger'),
            service(DeactivateExpiredLabelsService::class),
        ])
        ->tag('messenger.message_handler');
};
