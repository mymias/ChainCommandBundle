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

use Nimias\ChainCommandBundle\Exception\CommandChainCollisionException;

/**
 * Command chains registry
 *
 * Main storage for registered command chains data.
 */
class CommandChainsRegistry
{
    /**
     * @var array Registered command chains
     */
    private $commandChains = [];

    /**
     * @var array Registered subCommands for quick check
     */
    private $subCommandNames = [];

    /**
     * Adds one sub-command to command chain
     *
     * @param string $mainCommandName
     * @param string $subCommandName
     * @param int $priority
     * @param array $arguments
     */
    public function addSubCommandToChain(string $mainCommandName, string $subCommandName, int $priority, array $arguments = [])
    {
        $this->validate($mainCommandName, $subCommandName, $priority, $arguments);

        $this->commandChains[$mainCommandName][$priority][$subCommandName] = $arguments;
        $this->subCommandNames[$subCommandName] = $mainCommandName;
    }

    /**
     * Returns array of registered children commands for given command (or empty array if zero registered)
     *
     * Format of return data: [
     *      (int)<priority> => [
     *          (string)<child_command_name> => (array)<command_arguments>,
     *          (string)<child_command_name> => (array)<command_arguments>,
     *      ]
     * ]
     *
     * Example data: [
     *      10 => [
     *          'first:child-command.name' => ['argumentName1' => 'someValue', 'optionName2' => 'anotherVal'],
     *          'second:child-command.name' => ['argumentName2' => 'someValue2', 'optionName2' => 'anotherVal'],
     *      ]
     *      20 => [
     *          'third:child-command.name' => [],
     *          'fourth:child-command.name' => ['optionName2' => 'anotherVal'],
     *      ]
     * ]
     *
     * @param string $mainCommandName
     *
     * @return array
     */
    public function getCommandChain(string $mainCommandName): array
    {
        if(isset($this->commandChains[$mainCommandName])) {
            ksort($this->commandChains[$mainCommandName]);
            return $this->commandChains[$mainCommandName];
        }

        return [];
    }

    /**
     * Checks if given command registered as child of any command chain.
     *
     * Returns a name of parent command if registered, null otherwise
     *
     * @param string $childCommandName
     *
     * @return string|null
     */
    public function getParentCommandName(string $childCommandName)
    {
        return
            isset($this->subCommandNames[$childCommandName])
            ? $this->subCommandNames[$childCommandName]
            : null
        ;
    }

    /**
     * Validation checks for $this->addSubCommandToChain()
     *
     * @param string $mainCommandName
     * @param string $subCommandName
     * @param int $priority
     * @param array $arguments
     *
     * @throws CommandChainCollisionException
     */
    private function validate(string $mainCommandName, string $subCommandName, int $priority, array $arguments = [])
    {
        // Registered MAIN command MUST NOT be already registered as CHILD command somewhere.
        if(isset($this->subCommandNames[$mainCommandName])) {
            throw new CommandChainCollisionException(sprintf(
                'Command %s cannot be a parent command, because it already is subcommand of command %s',
                $mainCommandName,
                $this->subCommandNames[$mainCommandName]
            ));
        }

        // Registered CHILD command MUST NOT be registered as MAIN command somewhere.
        if(isset($this->commandChains[$subCommandName])) {
            throw new CommandChainCollisionException(sprintf(
                'Command %s cannot be a child command, because it already registered as parent command',
                $subCommandName
            ));
        }

        // Priority for child command must be in reasonable range
        if($priority < 0 || $priority > 1000000) {
            throw new \InvalidArgumentException(sprintf(
                'priority parameter must be between 0 and 1000000, %d given',
                $priority
            ));
        }

        // Arguments and options for child commands must be a scalar type, if presents
        foreach($arguments as $name => $argument) {
            if(!is_scalar($argument) && !is_null($argument) && ('' !== $argument)) {
                throw new \InvalidArgumentException(sprintf(
                    'Argument %s of child commands must have scalar or empty value',
                    $name
                ));
            }
        }
    }
}