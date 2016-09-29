<?php
/*
 * This file is part of the OroChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Oro\ChainCommandBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for test purpose
 */
class TestTwoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('oro.chain_bundle.test_command.one')
        ;
    }

    /**
     * Main command method.
     *
     * Prints greetings string.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Hi! This is command %s', $this->getName()));
    }
}