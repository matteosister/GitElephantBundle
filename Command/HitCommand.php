<?php

namespace Cypress\GitElephantBundle\Command;

use Cypress\GitElephantBundle\Collection\GitElephantRepositoryCollection;
use GitElephant\Objects\Branch;
use GitElephant\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HitCommand
 *
 * @category Command
 * @package  Cypress\GitElephantBundle\Command
 * @author   David Romaní <david@flux.cat>
 */
class HitCommand extends Command
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
     * Hit command configuration
     */
    protected function configure()
    {
        $this->setName('cypress:git:hit')
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
            ->setDescription('Merge without fast forward from source to destination branch, tag destination branch and push to all remotes')
            ->addOption(
                'no-push',
                null,
                InputOption::VALUE_NONE,
                'If set, the task won\'t push to remotes'
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
                'If set, command will apply to all repositories'
            )
            ->setHelp(
                <<<EOT
<info>cypress:git:hit</info> combo command to merge without fast forward option from source to destination branch, tag destination branch and push to all remotes. Only apply fisrt repository, use --all option to apply all repositories. Use --no-push to commit only on your local repository. Apply --fast-forward to disable no fast forward merge option.
EOT
            );
    }

    /**
     * Execute hit command
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
            '<info>Welcome to the Cypress GitElephantBundle hit command.</info>'
        );
        if ($input->getOption('no-push')) {
            $output->writeln(
                '<comment>--no-push option enabled (this option disable push to remotes)</comment>'
            );
        }

        /** @var GitElephantRepositoryCollection $rc */
        $rc = $this->repositories;

        if ($rc->count() == 0) {
            throw new \Exception('Must have at least one Git repository. See https://github.com/matteosister/GitElephantBundle#how-to-use');
        }

        // Merge
        $command = $this->getApplication()->find('cypress:git:merge');
        $arguments = array(
            'command' => 'cypress:git:merge',
            'source' => $input->getArgument('source'),
            'destination' => $input->getArgument('destination'),
            '--no-push' => $input->getOption('no-push'),
            '--fast-forward' => $input->getOption('fast-forward'),
            '--all' => $input->getOption('all'),
        );
        $inputMergeCommand = new ArrayInput($arguments);
        $returnCode = $command->run($inputMergeCommand, $output);
        if ($returnCode != 0) {
            $output->writeln('··· return code: ' . $returnCode);
        }

        // Tag
        $command = $this->getApplication()->find('cypress:git:tag');
        $arguments = array(
            'command' => 'cypress:git:tag',
            'tag' => $input->getArgument('tag'),
            'comment' => $input->getArgument('comment'),
            '--no-push' => $input->getOption('no-push'),
            '--all' => $input->getOption('all'),
        );
        $inputTagCommand = new ArrayInput($arguments);
        // tag must apply to destination branch
        /** @var Repository $repository */
        foreach ($rc as $key => $repository) {
            if ($key == 0) {
                /** @var Branch $destination */
                $destination = $repository->getBranch($input->getArgument('destination'));
                $repository->checkout($destination->getName());
            }
        }
        $returnCode = $command->run($inputTagCommand, $output);
        if ($returnCode != 0) {
            $output->writeln('··· return code: ' . $returnCode);
        }
        // change to source branch
        /** @var Repository $repository */
        foreach ($rc as $key => $repository) {
            if ($key == 0) {
                /** @var Branch $destination */
                $destination = $repository->getBranch($input->getArgument('source'));
                $repository->checkout($destination->getName());
            }
        }
    }
}
