<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Storefront\Struct\ProductLabel;

use Shopware\Core\Framework\Struct\Struct;

final class ProductLabelStruct extends Struct
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $color,
        protected int $priority,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
