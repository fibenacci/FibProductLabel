<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Unit\Core\Content\ProductLabel;

use Fib\ProductLabel\Core\Content\ProductLabel\Service\ProductLabelVisibilityService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

#[CoversClass(ProductLabelVisibilityService::class)]
class ProductLabelVisibilityServiceTest extends TestCase
{
    private ProductLabelVisibilityService $service;

    protected function setUp(): void
    {
        $this->service = new ProductLabelVisibilityService();
    }

    public function testPrepareCriteriaAddsCorrectFiltersAndSorting(): void
    {
        $criteria = new Criteria();

        $this->service->prepareCriteria($criteria);

        $labelCriteria = $criteria->getAssociation('fibProductLabels');

        // Check associations
        static::assertTrue($labelCriteria->hasAssociation('translations'));

        // Check active filter
        $filters = $labelCriteria->getFilters();
        static::assertCount(3, $filters); // active, validFrom, validTo

        static::assertInstanceOf(EqualsFilter::class, $filters[0]);
        static::assertSame('active', $filters[0]->getField());
        static::assertTrue($filters[0]->getValue());

        // Check Sorting
        $sortings = $labelCriteria->getSorting();
        static::assertCount(1, $sortings);
        static::assertInstanceOf(FieldSorting::class, $sortings[0]);
        static::assertSame('priority', $sortings[0]->getField());
        static::assertSame(FieldSorting::DESCENDING, $sortings[0]->getDirection());
    }
}
