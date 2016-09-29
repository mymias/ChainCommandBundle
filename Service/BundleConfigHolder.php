<?php
/*
 * This file is part of the OroChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Oro\ChainCommandBundle\Service;

/**
 * Holder of bundle configuration
 *
 * Used in test environment for testing purpose.
 *
 * @See \Oro\ChainCommandBundle\DependencyInjection\OroChainCommandExtension::load
 * @See \Oro\ChainCommandBundle\Features\Context\FeatureContext::commandRegisteredInConfigFileAsMemberOfChainForCommandWithParams
 */
class BundleConfigHolder
{
    private $config = [];

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}