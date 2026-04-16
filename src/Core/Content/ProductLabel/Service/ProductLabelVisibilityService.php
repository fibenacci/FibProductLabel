<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Service;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Log\Package;

#[Package('framework')]
class ProductLabelVisibilityService
{
    public function prepareCriteria(Criteria $criteria): void
    {
        $now = (new \DateTimeImmutable())->format(\DATE_ATOM);

        $labelCriteria = $criteria->getAssociation('fibProductLabels');
        $labelCriteria->addAssociation('translations');

        $labelCriteria->addFilter(
            new EqualsFilter('active', true),
            new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsFilter('validFrom', null),
                new RangeFilter('validFrom', [RangeFilter::LTE => $now]),
            ]),
            new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsFilter('validTo', null),
                new RangeFilter('validTo', [RangeFilter::GTE => $now]),
            ])
        );

        $labelCriteria->addSorting(new FieldSorting('priority', FieldSorting::DESCENDING));
    }
}
