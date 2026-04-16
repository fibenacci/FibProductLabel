<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\ScheduledTask;

use Fib\ProductLabel\Core\Content\ProductLabel\Service\DeactivateExpiredLabelsService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: DeactivateExpiredLabelsTask::class)]
class DeactivateExpiredLabelsTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        LoggerInterface $logger,
        private readonly DeactivateExpiredLabelsService $deactivateExpiredLabelsService,
    ) {
        parent::__construct($scheduledTaskRepository, $logger);
    }

    public function run(): void
    {
        $this->deactivateExpiredLabelsService->deactivateExpired(Context::createDefaultContext());
    }
}
