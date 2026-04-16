<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Tests\Unit\Core\Content\ProductLabel;

use Fib\ProductLabel\Core\Content\ProductLabel\ProductLabelCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProductLabelCollection::class)]
class ProductLabelCollectionTest extends TestCase
{
    public function testCollectionCanBeInstantiated(): void
    {
        $collection = new ProductLabelCollection();
        static::assertInstanceOf(ProductLabelCollection::class, $collection);
    }
}
