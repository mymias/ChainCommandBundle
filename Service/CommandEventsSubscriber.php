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

use Nimias\ChainCommandBundle\Helper\OutputBufferHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\ConsoleEvents;

/**
 * Command chains processor
 *
 * Hooks console command executing events and process registered child commands for requested command
 */
class CommandEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var CommandChainsRegistry
     */
    private $chainsRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * @var bool Marker if it must be performed logging during operations
     */
    private $isLoggingEnabled = false;

    /**
     * CommandEventsSubscriber constructor.
     *
     * @param CommandChainsRegistry $chainsRegistry
     * @param LoggerInterface|null $logger
     */
    public function __construct(CommandChainsRegistry $chainsRegistry, LoggerInterface $logger = null)
    {
        $this->chainsRegistry = $chainsRegistry;
        $this->logger = $logger;
        stream_filter_register('console_output_buffer_filter', '\Nimias\ChainCommandBundle\Helper\ConsoleOutputFilterHelper');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            ConsoleEvents::COMMAND => [
                ['processCommand', 10],
            ],
            ConsoleEvents::TERMINATE => [
                ['processTerminate', 10],
            ],
        ];
    }

    /**
     * Listener for event ConsoleEvents::COMMAND (console.command)
     *
     * Main purpose: disabling execution of registered children commands
     *
     * @param ConsoleCommandEvent $event
     */
    public function processCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();
        $requestedCommandName = $command->getName();
        $output = $event->getOutput();

        // Check if given command registered as child and has parent command
        $parentCommandName = $this->chainsRegistry->getParentCommandName($command->getName());

        if($parentCommandName) {
            $output->writeln(sprintf(
                "Error: %s command is a member of %s command chain and cannot be executed on its own.",
                $requestedCommandName,
                $parentCommandName
            ));

            $event->disableCommand();
            return;
        }

        // Check if it's a main command in some registered chain
        $childrenCommandsChain = $this->chainsRegistry->getCommandChain($requestedCommandName);

        if(!empty($childrenCommandsChain)) {

            // Copy console command output to additional buffer for logging later
            if($output instanceof StreamOutput) {
                stream_filter_append($output->getStream(), 'console_output_buffer_filter', STREAM_FILTER_WRITE);
            }

            // Log some info
            if($this->isLoggingEnabled) {

                $this->logger->info(sprintf(
                    '%s is a master command of a command chain that has registered member commands',
                    $requestedCommandName
                ));

                foreach($childrenCommandsChain as $priority => $childrens) {
                    foreach($childrens as $childCommandName => $args) {
                        $this->logger->info(sprintf(
                            '%s registered as a member of %s command chain',
                            $childCommandName,
                            $requestedCommandName
                        ));
                    }
                }

                $this->logger->info(sprintf(
                    'Executing %s command itself first:',
                    $requestedCommandName
                ));
            }
        }
    }

    /**
     * Listener for event ConsoleEvents::TERMINATE (console.terminate)
     *
     * Executes registered child commands after execution of main command
     *
     * @param ConsoleTerminateEvent $event
     *
     * @throws \Exception
     */
    public function processTerminate(ConsoleTerminateEvent $event)
    {
        $command = $event->getCommand();
        $requestedCommandName = $command->getName();

        // Get chain of registered child commands for requested main command
        $childrenCommandsChain = $this->chainsRegistry->getCommandChain($requestedCommandName);

        if(!empty($childrenCommandsChain)) {
            $output = $event->getOutput();
            $application = $command->getApplication();

            // Log some info
            if($this->isLoggingEnabled) {
                // Log main-command output
                $this->logger->info(OutputBufferHelper::fetch());
                // Log start of executing child commands
                $this->logger->info(sprintf(
                    'Executing %s chain members:',
                    $requestedCommandName
                ));
            }

            // Perform children commands queue, ordered by priority asc.
            foreach($childrenCommandsChain as $priority => $childrenCommands) {

                foreach($childrenCommands as $childCommandName => $args) {

                    try {
                        // Log child command start
                        if($this->isLoggingEnabled) {
                            $this->logger->info(sprintf('Executing %s command:', $childCommandName));
                        }

                        // Execute child command
                        $childCommand = $application->get($childCommandName);
                        $childInput = new ArrayInput($args);
                        $childReturnCode = $childCommand->run($childInput, $output);

                        // Log child command output
                        if($this->isLoggingEnabled) {
                            $this->logger->info(OutputBufferHelper::fetch());
                        }

                    } catch (\Exception $exception) {
                        // Log error message
                        if($this->isLoggingEnabled) {
                            $this->logger->error($exception->getMessage());
                        }

                        throw $exception;
                    }
                }
            }

            // Log some info
            if($this->isLoggingEnabled) {
                $this->logger->info(sprintf(
                    'Execution of %s chain completed.',
                    $requestedCommandName
                ));
            }

        }
    }

    /**
     * Logging switcher
     *
     * @param bool $isLoggingEnabled
     */
    public function setLoggingEnabled(bool $isLoggingEnabled)
    {
        $this->isLoggingEnabled = $isLoggingEnabled && ($this->logger instanceof LoggerInterface);
    }
}