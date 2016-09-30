<?php
/*
 * This file is part of the NimiasChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimias\ChainCommandBundle\Service;

/**
 * Holder of bundle configuration
 *
 * Used in test environment for testing purpose.
 *
 * @See \Nimias\ChainCommandBundle\DependencyInjection\OroChainCommandExtension::load
 * @See \Nimias\ChainCommandBundle\Features\Context\FeatureContext::commandRegisteredInConfigFileAsMemberOfChainForCommandWithParams
 */
class BundleConfigHolder
{
    private $config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}