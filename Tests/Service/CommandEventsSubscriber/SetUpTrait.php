<?php
/*
 * This file is part of the NimiasChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimias\ChainCommandBundle\Tests\Service\CommandEventsSubscriber;

use Nimias\ChainCommandBundle\Service\CommandChainsRegistry;
use Nimias\ChainCommandBundle\Service\CommandEventsSubscriber;
use Psr\Log\LoggerInterface;

/**
 * SetUp trait for CommandEventsSubscriber test classes
 *
 * @See \Nimias\ChainCommandBundle\Tests\Service\CommandEventsSubscriber\ProcessCommandTest
 * @See \Nimias\ChainCommandBundle\Tests\Service\CommandEventsSubscriber\ProcessTerminateTest
 */
trait SetUpTrait {
    /**
     * @var string
     */
    private $firstCommandName = 'first:command.name';

    /**
     * @var string
     */
    private $secondCommandName = 'second:command.name';

    /**
     * @var string
     */
    private $thirdCommandName = 'third:command.name';

    /**
     * @var string
     */
    private $fourthCommandName = 'fourth:command.name';

    /**
     * @var CommandEventsSubscriber
     */
    private $commandSubscriber;

    private $logInfo = [];

    protected function setUp()
    {
        parent::setUp();
        $chainsRegistry = new CommandChainsRegistry();

        $chainsRegistry->addSubCommandToChain(
            $this->firstCommandName,
            $this->secondCommandName,
            1
        );
        $chainsRegistry->addSubCommandToChain(
            $this->firstCommandName,
            $this->thirdCommandName,
            15
        );
        $chainsRegistry->addSubCommandToChain(
            $this->firstCommandName,
            $this->fourthCommandName,
            7,
            ['name' => 'value']
        );

        $this->logInfo = [];

        $logger = new class($this) implements LoggerInterface {

            private $externalLogger;

            public function __construct($externalLogger)
            {
                $this->externalLogger = $externalLogger;
            }

            public function emergency($message, array $context = array()){}
            public function alert($message, array $context = array()){}
            public function critical($message, array $context = array()){}
            public function error($message, array $context = array()){}
            public function warning($message, array $context = array()){}
            public function notice($message, array $context = array()){}
            public function debug($message, array $context = array()){}
            public function log($level, $message, array $context = array()){}
            public function info($message, array $context = array())
            {
                $this->externalLogger->info($message, $context);
            }
        };

        $this->commandSubscriber = new CommandEventsSubscriber($chainsRegistry, $logger);
    }

    public function info($message, $context)
    {
        $this->logInfo[] = [$message, $context];
    }
}