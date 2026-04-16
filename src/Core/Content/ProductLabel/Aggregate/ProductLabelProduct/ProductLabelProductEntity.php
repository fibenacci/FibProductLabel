<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\Log\Package;

#[Package('framework')]
class ProductLabelProductEntity extends Entity
{
    protected string $productLabelId;

    protected string $productId;

    protected string $productVersionId;

    public function getProductLabelId(): string
    {
        return $this->productLabelId;
    }

    public function setProductLabelId(string $productLabelId): void
    {
        $this->productLabelId = $productLabelId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getProductVersionId(): string
    {
        return $this->productVersionId;
    }

    public function setProductVersionId(string $productVersionId): void
    {
        $this->productVersionId = $productVersionId;
    }
}
