<?php
namespace Watcher\Command\Monolog;

use Watcher\Command\Monolog\Handler;
use Watcher\Watcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Follow command for Monolog
 *
 * @author MilesChou <jangconan@gmail.com>
 */
class Follow extends Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('monolog:follow')
            ->setDescription('Follow file tail')
            ->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Watch file')
            ->addOption('--lines', '-l', InputOption::VALUE_REQUIRED, 'Show tail lines');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');
        $lines = (int)$input->getOption('lines');

        $watcher = new Watcher();

        $callback = new Handler\Follow($output);
        $callback->setLines($lines);

        $watcher->addFiles($files);
        $watcher->watch($callback);
    }
}
