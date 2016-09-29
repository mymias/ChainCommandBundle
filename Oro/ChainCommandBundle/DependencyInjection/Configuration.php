<?php
/*
 * This file is part of the OroChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Oro\ChainCommandBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder for "oro_chain_command" section
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('oro_chain_command');

        $rootNode
            ->children()
                ->arrayNode('logging')
                    ->canBeDisabled()
                ->end()
                ->arrayNode('command_chains')
                    ->prototype('array')                    // main commands - keys of arrays
                        ->prototype('array')                // children commands - keys of children array
                            ->children()
                                ->integerNode('priority')   // priority param is required
                                    ->defaultValue(10)
                                    ->isRequired()
                                ->end()
                            ->end()
                            ->prototype('scalar')->end()    // can be many arguments of commands
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
