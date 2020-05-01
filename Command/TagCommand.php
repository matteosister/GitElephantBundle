<?php

namespace Cypress\GitElephantBundle\Command;

use GitElephant\Repository;
use GitElephant\Objects\Remote;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Cypress\GitElephantBundle\Collection\GitElephantRepositoryCollection;

/**
 * Class TagCommand
 *
 * @category Command
 * @package  Cypress\GitElephantBundle\Command
 * @author   David Romaní <david@flux.cat>
 */
class TagCommand extends Command
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
     * Tag command configuration
     */
    protected function configure()
    {
        $this->setName('cypress:git:tag')
            ->setDefinition(
                array(
                    new InputArgument(
                        'tag',
                        InputArgument::REQUIRED,
                        'Tag title'
                    ),
                    new InputArgument(
                        'comment',
                        InputArgument::OPTIONAL,
                        'Tag comment'
                    ),
                )
            )
            ->setDescription('Tag current commit and push to all remotes')
            ->addOption(
                'no-push',
                null,
                InputOption::VALUE_NONE,
                'If set, the task won\'t push tag to remotes'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, will tag to all repositories'
            )
            ->setHelp(
                <<<EOT
<info>cypress:git:tag</info> command will tag your current commit and push current branch to all remotes. Only apply fisrt repository, use --all option to apply all repositories. Use --no-push to commit only on your local repository.
EOT
            );
    }

    /**
     * Execute tag command
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
            '<info>Welcome to the Cypress GitElephantBundle tag command.</info>'
        );
        if ($input->getOption('no-push')) {
            $output->writeln(
                '<comment>--no-push option enabled (this option disable push tag to remotes)</comment>'
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
                $repository->createTag($input->getArgument('tag'), null, $input->getArgument('comment') ? $input->getArgument('comment') : null);
                if (!$input->getOption('no-push')) {
                    /** @var Remote $remote */
                    foreach ($repository->getRemotes() as $remote) {
                        $repository->push($remote->getName(), $repository->getMainBranch()->getName()); // Push current branch to all remotes
                    }
                }
                $output->writeln('Set tag ' . $input->getArgument('tag') . ' to local repository ' . $repository->getName() . (!$input->getOption('no-push') ? ' and pushed to all remotes.' : ''));
            }
        }
    }
}
