<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1776357537CreateProductLabelSchema extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1776357537;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<SQL
            CREATE TABLE IF NOT EXISTS `fib_product_label` (
                `id` BINARY(16) NOT NULL,
                `color` VARCHAR(16) NOT NULL,
                `priority` INT NOT NULL DEFAULT 0,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `valid_from` DATETIME(3) NULL,
                `valid_to` DATETIME(3) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            SQL
        );

        $connection->executeStatement(<<<SQL
            CREATE TABLE IF NOT EXISTS `fib_product_label_translation` (
                `fib_product_label_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`fib_product_label_id`, `language_id`),
                CONSTRAINT `fk.fib_product_label_translation.fib_product_label_id` FOREIGN KEY (`fib_product_label_id`)
                    REFERENCES `fib_product_label` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.product_label_translation.language_id` FOREIGN KEY (`language_id`)
                    REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            SQL
        );

        $connection->executeStatement(<<<SQL
            CREATE TABLE IF NOT EXISTS `fib_product_label_product` (
                `fib_product_label_id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NOT NULL,
                `product_version_id` BINARY(16) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                PRIMARY KEY (`fib_product_label_id`, `product_id`, `product_version_id`),
                CONSTRAINT `fk.product_label_product.fib_product_label_id` FOREIGN KEY (`fib_product_label_id`)
                    REFERENCES `fib_product_label` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.fib_product_label_product.product_id` FOREIGN KEY (`product_id`, `product_version_id`)
                    REFERENCES `product` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            SQL
        );
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
