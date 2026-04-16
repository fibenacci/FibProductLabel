<?php

declare(strict_types=1);

namespace Fib\ProductLabel\Core\Content\ProductLabel\Commands;

use Fib\ProductLabel\Core\Content\ProductLabel\Service\DeactivateExpiredLabelsService;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'fib:deactivate:expired:labels',
    description: 'Deactivate expired labels',
)]
final class DeactivateExpiredLabelCommand extends Command
{
    public function __construct(
        private readonly DeactivateExpiredLabelsService $deactivateExpiredLabelsService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Deactivating expired product labels');

        $this->deactivateExpiredLabelsService->deactivateExpired(Context::createDefaultContext());

        $io->success('Deactivation completed.');

        return Command::SUCCESS;
    }
}
