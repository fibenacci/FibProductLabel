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
    /**
     * Prepares criteria to only fetch active and currently valid labels, 
     * sorted by their defined priority.
     */
    public function prepareCriteria(Criteria $criteria): void
    {
        // Use DATE_ATOM to ensure compatibility with MySQL datetime fields in the DAL.
        $now = (new \DateTimeImmutable())->format(\DATE_ATOM);

        // Fetch the labels via the extension association.
        $labelCriteria = $criteria->getAssociation('fibProductLabels');
        $labelCriteria->addAssociation('translations');

        // We only want labels that are marked as active and where the current time 
        // falls within the validFrom and validTo range (if they are set).
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

        // Sort by priority descending to show the most important labels first in the UI.
        $labelCriteria->addSorting(new FieldSorting('priority', FieldSorting::DESCENDING));
    }
}
