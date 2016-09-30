<?php
/*
 * This file is part of the NimiasChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimias\ChainCommandBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages configuration for NimiasChainCommandBundle.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class NimiasChainCommandExtension extends Extension
{
    /**
     * Loads and process NimiasChainCommandBundle configuration from section "nimias_chain_command" in config files.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // For testing purpose create service, that holds this bundle configuration
        if('test' === $container->getParameter('kernel.environment')) {
            $configHolderServiceDefinition = $container->register(
                'nimias_chain_command.config_holder',
                'Nimias\\ChainCommandBundle\\Service\\BundleConfigHolder'
            );
            $configHolderServiceDefinition->addArgument($config);
        }

        // Load predefined bundle services
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // Process bundle configuration

        // If logging enabled
        if($this->isConfigEnabled($container, $config['logging'])) {
            // Switch on logging in main bundle service
            $definition = $container->getDefinition('nimias_chain_command.processor');
            $definition->addMethodCall('setLoggingEnabled', [true]);
        }

        // If were set up command chains in config
        if(!empty($config['command_chains'])) {
            // Fill up command chains registry service
            $definition = $container->getDefinition('nimias_chain_command.registry');

            foreach($config['command_chains'] as $mainCommandName => $childrenCommands) {

                foreach($childrenCommands as $subCommandName => $subCommandArgs) {
                    $definition->addMethodCall('addSubCommandToChain', [
                        $mainCommandName,
                        $subCommandName,
                        $subCommandArgs['priority'],
                        array_diff_key($subCommandArgs, ['priority' => ''])
                    ]);
                }
            }
        }
    }
}
