<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Command;

use Ayacoo\AyacooSoundcloud\Service\UpdateMetadataService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateMetadataCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Updates the Soundcloud metadata');
        $this->addOption(
            'limit',
            null,
            InputOption::VALUE_OPTIONAL,
            'Defines the number of Soundcloud audios to be checked',
            10
        );
    }

    public function __construct(
        protected readonly UpdateMetadataService $updateMetadataService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = (int)($input->getOption('limit') ?? 10);

        $this->updateMetadataService->process($limit, $io);

        return Command::SUCCESS;
    }
}
