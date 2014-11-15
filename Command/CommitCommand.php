<?php

namespace Cypress\GitElephantBundle\Command;

use Cypress\GitElephantBundle\Collection\GitElephantRepositoryCollection;
use GitElephant\Repository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

/**
 * Class CommitCommand
 *
 * @category Command
 * @package  Cypress\GitElephantBundle\Command
 * @author   David Romaní <david@flux.cat>
 */
class CommitCommand extends ContainerAwareCommand
{
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
            ->setDescription('Commit and push to remote repository')
            ->addOption(
                'no-push',
                null,
                InputOption::VALUE_NONE,
                'If set, the task won\'t push tag to remote repository'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, will commit to all repositories'
            )
            ->setHelp(
                <<<EOT
<info>cypress:git:commit</info> command will commit and push to remote repository. Only apply fisrt repository, use --all option to apply all repositories. Use --no-push to commit only on your local repository.
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
                '<comment>--no-push option enabled (this option disable push commit to remote repository)</comment>'
            );
        }

        /** @var GitElephantRepositoryCollection $rc */
        $rc = $this->getContainer()->get('git_repositories');

        if ($rc->count() == 0) {
            throw new \Exception('Must have at least one Git repository. See https://github.com/matteosister/GitElephantBundle#how-to-use');
        }

        /** @var Repository $repository */
        foreach ($rc as $key => $repository) {
            if ($key == 0 || $key > 0 && $input->getOption('all')) {
                $repository->commit($input->getArgument('message'));
                if (!$input->getOption('no-push')) {
                    $repository->push();
                }
                $output->writeln('Set commit to local repository ' . $repository->getName() . (!$input->getOption('no-push') ? ' and pushed to remote.' : ''));
            }
        }
    }
}
