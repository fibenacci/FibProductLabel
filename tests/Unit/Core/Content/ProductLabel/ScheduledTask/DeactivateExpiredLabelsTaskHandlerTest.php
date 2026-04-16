<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Unit\Core\Content\ProductLabel\ScheduledTask;

use Fib\ProductLabel\Core\Content\ProductLabel\ScheduledTask\DeactivateExpiredLabelsTaskHandler;
use Fib\ProductLabel\Core\Content\ProductLabel\Service\DeactivateExpiredLabelsService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

#[CoversClass(DeactivateExpiredLabelsTaskHandler::class)]
class DeactivateExpiredLabelsTaskHandlerTest extends TestCase
{
    public function testRunCallsDeactivateExpiredLabelsService(): void
    {
        $service = $this->createMock(DeactivateExpiredLabelsService::class);
        $service->expects(static::once())
            ->method('deactivateExpired')
            ->with(static::isInstanceOf(Context::class));

        $handler = $this->makeHandler($service);
        $handler->run();
    }

    private function makeHandler(DeactivateExpiredLabelsService $service): DeactivateExpiredLabelsTaskHandler
    {
        return new DeactivateExpiredLabelsTaskHandler(
            $this->createMock(EntityRepository::class),
            new NullLogger(),
            $service
        );
    }
}
