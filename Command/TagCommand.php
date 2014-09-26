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
 * Class TagCommand
 *
 * @category Command
 * @package  Cypress\GitElephantBundle\Command
 * @author   David Romaní <david@flux.cat>
 */
class TagCommand extends ContainerAwareCommand
{
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
            ->setDescription('Tag current commit and push to remote repository')
            ->addOption(
                'no-push',
                null,
                InputOption::VALUE_NONE,
                'If set, the task won\'t push tag to remote repository'
            )
            ->setHelp(
                <<<EOT
<info>cypress:git:tag</info> command will tag your current commit and (optionally) push to remote repository.
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
                '<comment>--no-push option enabled (this option disable push tag to remote repository)</comment>'
            );
        }

        /** @var GitElephantRepositoryCollection $rc */
        $rc = $this->getContainer()->get('git_repositories');

        if ($rc->count() == 0) {
            throw new \Exception('Must have at least one Git repository. See https://github.com/matteosister/GitElephantBundle#how-to-use');
        }

        /** @var Repository $repository */
        foreach ($rc as $repository) {
            $output->writeln($repository->getName());
        }
    }
}
