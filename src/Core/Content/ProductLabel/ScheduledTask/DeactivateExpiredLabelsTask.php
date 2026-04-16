<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class DeactivateExpiredLabelsTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'fib_product_label.deactivate_expired';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // Once a day
    }
}
