<?php

namespace Cypress\GitElephantBundle\Command;

use Cypress\GitElephantBundle\Collection\GitElephantRepositoryCollection;
use GitElephant\Objects\Remote;
use GitElephant\Repository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommitCommand
 *
 * @category Command
 * @package  Cypress\GitElephantBundle\Command
 * @author   David Romaní <david@flux.cat>
 */
class CommitCommand extends Command
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
     * Commit command configuration
     */
    protected function configure()
    {
        $this->setName('cypress:git:commit')
            ->setDefinition(
                array(
                    new InputArgument(
                        'message',
                        InputArgument::REQUIRED,
                        'Commit message'
                    ),
                )
            )
            ->setDescription('Commit and push current branch to all remotes')
            ->addOption(
                'no-push',
                null,
                InputOption::VALUE_NONE,
                'If set, the task won\'t push commit to remotes'
            )
            ->addOption(
                'no-stage-all',
                null,
                InputOption::VALUE_NONE,
                'If set, the task won\'t stage all the working tree content'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, will commit and push to all repositories'
            )
            ->setHelp(
                <<<EOT
<info>cypress:git:commit</info> command will commit and push current branch to all remotes. Only apply fisrt repository, use --all option to apply all repositories. Use --no-push to commit only on your local repository without pushing. Use --no-stage-all to disable stage all the working tree content feature.
EOT
            );
    }

    /**
     * Execute commit command
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
            '<info>Welcome to the Cypress GitElephantBundle commit command.</info>'
        );
        if ($input->getOption('no-push')) {
            $output->writeln(
                '<comment>--no-push option enabled (this option disable push commit to remotes)</comment>'
            );
        }

        if ($this->repositories->count() == 0) {
            throw new \Exception('Must have at least one Git repository. See https://github.com/matteosister/GitElephantBundle#how-to-use');
        }

        /** @var Repository $repository */
        foreach ($this->repositories as $key => $repository) {
            if ($key == 0 || $key > 0 && $input->getOption('all')) {
                $repository->commit($input->getArgument('message'), !$input->getOption('no-stage-all'));
                if (!$input->getOption('no-push')) {
                    /** @var Remote $remote */
                    foreach ($repository->getRemotes() as $remote) {
                        $repository->push($remote->getName(), $repository->getMainBranch()->getName()); // Push last current branch commit to all remotes
                    }
                }
                $output->writeln('New commit to local repository created ' . $repository->getName() . (!$input->getOption('no-push') ? ' and pushed to all remotes.' : ''));
            }
        }
    }
}
