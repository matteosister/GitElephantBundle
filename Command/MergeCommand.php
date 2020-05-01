<?php

namespace Cypress\GitElephantBundle\Command;

use Cypress\GitElephantBundle\Collection\GitElephantRepositoryCollection;
use GitElephant\Objects\Branch;
use GitElephant\Objects\Remote;
use GitElephant\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MergeCommand
 *
 * @category Command
 * @package  Cypress\GitElephantBundle\Command
 * @author   David Romaní <david@flux.cat>
 */
class MergeCommand extends Command
{
    /**
     * The collection of repositories from which one will be commiting
     *
     * @var GitElephantRepositoryCollection
     */
    private $repositories;

    public function __construct(GitElephantRepositoryCollection $c)
    {
        $this->repositories = $c;
        parent::__construct();
    }

    /**
     * Merge command configuration
     */
    protected function configure()
    {
        $this->setName('cypress:git:merge')
            ->setDefinition(
                array(
                    new InputArgument(
                        'source',
                        InputArgument::OPTIONAL,
                        'Source branch',
                        'devel' // default source branch
                    ),
                    new InputArgument(
                        'destination',
                        InputArgument::OPTIONAL,
                        'Destination branch',
                        'master' // default destination branch
                    ),
                )
            )
            ->setDescription('Merge without fast forward from source to destination branch and push destination branch to all remotes')
            ->addOption(
                'no-push',
                null,
                InputOption::VALUE_NONE,
                'If set, the task won\'t push destination branch to remotes'
            )
            ->addOption(
                'fast-forward',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will use fast forward merge option'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, will merge and push to all repositories'
            )
            ->setHelp(
                <<<EOT
<info>cypress:git:merge</info> command will merge without fast forward option from source to destination branch and push destination branch to all remotes. Only apply fisrt repository, use --all option to apply all repositories. Use --no-push to commit only on your local repository. Apply --fast-forward to disable no fast forward merge option.
EOT
            );
    }

    /**
     * Execute merge command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome
        $output->writeln(
            '<info>Welcome to the Cypress GitElephantBundle merge command.</info>'
        );
        if ($input->getOption('no-push')) {
            $output->writeln(
                '<comment>--no-push option enabled (this option disable push destination branch to remotes)</comment>'
            );
        }

        /** @var GitElephantRepositoryCollection $rc */
        $rc = $this->repositories;

        if ($rc->count() == 0) {
            throw new \Exception('Must have at least one Git repository. See https://github.com/matteosister/GitElephantBundle#how-to-use');
        }

        /** @var Repository $repository */
        foreach ($rc as $key => $repository) {
            if ($key == 0 || $key > 0 && $input->getOption('all')) {
                /** @var Branch $source */
                $source = $repository->getBranch($input->getArgument('source'));
                if (is_null($source)) {
                    throw new \Exception('Source branch ' . $input->getArgument('source') . ' doesn\'t exists');
                }
                /** @var Branch $destination */
                $destination = $repository->getBranch($input->getArgument('destination'));
                if (is_null($destination)) {
                    throw new \Exception('Destination branch ' . $input->getArgument('destination') . ' doesn\'t exists');
                }
                $repository->checkout($destination->getName());
                $repository->merge($source, '', (!$input->getOption('fast-forward') ? 'no-ff' : 'ff-only'));
                if (!$input->getOption('no-push')) {
                    /** @var Remote $remote */
                    foreach ($repository->getRemotes() as $remote) {
                        $repository->push($remote->getName(), $repository->getMainBranch()->getName()); // Push destination branch to all remotes
                    }
                }
                $repository->checkout($input->getArgument('source'));
                $output->writeln('Merge from ' . $input->getArgument('source') . ' branch to ' . $input->getArgument('destination') . ' done' . (!$input->getOption('no-push') ? ' and pushed to all remotes.' : ''));
            }
        }
    }
}
