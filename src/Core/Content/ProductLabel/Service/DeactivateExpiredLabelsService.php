<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Service;

use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

class DeactivateExpiredLabelsService
{
    /**
     * @param EntityRepository<ProductLabelCollection> $productLabelRepository
     */
    public function __construct(
        private EntityRepository $productLabelRepository,
    ) {
    }

    public function deactivateExpired(Context $context): void
    {
        $now = (new \DateTimeImmutable())->format(\DATE_ATOM);

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new RangeFilter('validTo', [
                RangeFilter::LT => $now,
            ])
        );

        $expiredLabelIds = $this->productLabelRepository->searchIds($criteria, $context);

        $updates = array_map(
            static fn (string $id): array => [
                'id'     => $id,
                'active' => false,
            ],
            $expiredLabelIds->getIds()
        );

        if (!empty($updates)) {
            $this->productLabelRepository->update($updates, $context);
        }
    }
}
