<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Unit\Core\Content\ProductLabel\Service;

use Fib\ProductLabel\Core\Content\ProductLabel\Service\DeactivateExpiredLabelsService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

#[CoversClass(DeactivateExpiredLabelsService::class)]
class DeactivateExpiredLabelsServiceTest extends TestCase
{
    public function testDeactivateExpiredUpdatesFoundLabels(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $context    = Context::createDefaultContext();

        $ids  = ['id-1', 'id-2'];
        $data = [];
        foreach ($ids as $id) {
            $data[$id] = ['primaryKey' => $id, 'data' => []];
        }

        $idSearchResult = new IdSearchResult(
            2,
            $data,
            new Criteria(),
            $context
        );

        $repository->expects(static::once())
            ->method('searchIds')
            ->willReturn($idSearchResult);

        $repository->expects(static::once())
            ->method('update')
            ->with(
                [
                    ['id' => 'id-1', 'active' => false],
                    ['id' => 'id-2', 'active' => false],
                ],
                $context
            );

        $service = new DeactivateExpiredLabelsService($repository);
        $service->deactivateExpired($context);
    }

    public function testDeactivateExpiredDoesNothingIfNoLabelsFound(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $context    = Context::createDefaultContext();

        $idSearchResult = new IdSearchResult(0, [], new Criteria(), $context);

        $repository->expects(static::once())
            ->method('searchIds')
            ->willReturn($idSearchResult);

        $repository->expects(static::never())
            ->method('update');

        $service = new DeactivateExpiredLabelsService($repository);
        $service->deactivateExpired($context);
    }
}
