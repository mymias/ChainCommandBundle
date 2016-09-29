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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages configuration for OroChainCommandBundle.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class OroChainCommandExtension extends Extension
{
    /**
     * Loads and process OroChainCommandBundle configuration from section "oro_chain_command" in config files.
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
            $configHolderServiceDefinition = new Definition('Oro\ChainCommandBundle\Service\BundleConfigHolder');
            $configHolderServiceDefinition->addMethodCall('setConfig', $config);
            $container->setDefinition(
                'oro_chain_command.config_holder',
                $configHolderServiceDefinition
            );
        }

        // Load predefined bundle services
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // Process bundle configuration

            // If logging enabled
        if($this->isConfigEnabled($container, $config['logging'])) {
            // Switch on logging in main bundle service
            $definition = $container->getDefinition('oro_chain_command.processor');
            $definition->addMethodCall('setLoggingEnabled', [true]);
        }

            // If set up command chains in config
        if(!empty($config['command_chains'])) {
            // Fill up command chains registry service
            $definition = $container->getDefinition('oro_chain_command.registry');

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
