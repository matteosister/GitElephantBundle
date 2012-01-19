<?php

namespace Cypress\GitElephantBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cypress_git_elephant');

        $rootNode
            ->children()
                ->scalarNode('repository_path')->isRequired()->end()
                ->scalarNode('binary_path')->defaultValue('/usr/bin/git')->end()
                ->scalarNode('profiler_repository_path')->defaultValue('%kernel.root_dir%/../')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
