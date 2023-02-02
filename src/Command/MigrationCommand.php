<?php

namespace App\Command;

use App\Tools\DirectoryResolver;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\YamlFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends Command
{
    protected static $defaultName = 'app:migrations:diff';
    protected $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
    }

    protected function configure()
    {
        $this
            ->setName('app:migrations:diff')
            ->setDescription('Proxy to launch doctrine:migrations:diff command as it would require a "configuration"
             option, and we can not define em/connection in config.')
            ->addArgument('em', InputArgument::REQUIRED, 'name of entity manager to handle.')
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute migration as dry none')
            ->addOption('query-time', null, InputOption::VALUE_NONE, 'Time all the queries individually')
            ->addOption('allow-no-migration', null, InputOption::VALUE_NONE, 'Does not throw an exception if no migration is available (CD)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newInput = new ArrayInput([]);
        $newInput->setInteractive($input->isInteractive());
        $otherCommand = new DiffCommand($this->getDependencyFactory($input));
        $otherCommand->run($newInput, $output);
        /*$io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');*/

        return Command::SUCCESS;
    }

    private function getDependencyFactory(InputInterface $input)
    {
        $em = $this->doctrine->getManager($input->getArgument('em'));
        $config = new YamlFile(
            trim(DirectoryResolver::getDirectory('config/packages/doctrine_migration/' . $input->getArgument('em') . '.yaml'), '/')
        );
        return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($em));
    }
}
