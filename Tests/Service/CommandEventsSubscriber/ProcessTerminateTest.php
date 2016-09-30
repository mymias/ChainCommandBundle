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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Tests for CommandEventsSubscriber::processTerminate() method
 *
 * @See \Nimias\ChainCommandBundle\Service\CommandEventsSubscriber::processTerminate
 */
class ProcessTerminateTest extends \PHPUnit_Framework_TestCase
{
    use SetUpTrait;

    // Test it should throw Exception for non-exists commands which registered in chains
    public function testItShouldThrowExceptionForNonExistsCommandsWhichRegisteredInChains()
    {
        $this->expectException('Symfony\Component\Console\Exception\CommandNotFoundException');

        $application = $this->createMock(Application::class);
        $application->method('get')->will($this->throwException(new CommandNotFoundException('Command not found')));

        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn('first:command.name');
        $command->method('getApplication')->willReturn($application);

        $event = $this->createMock(ConsoleTerminateEvent::class);
        $event->method('getCommand')->willReturn($command);

        $result = $this->commandSubscriber->processTerminate($event);
    }

    // Test it should throw Exception when missing a required parameter for child command
    public function testItShouldThrowExceptionWhenMissingARequiredParameterForChildCommand()
    {
        $this->markTestSkipped();
    }

    // Test it should display correct output for correctly formed chain of commands
    public function testItShouldDisplayCorrectOutputForCorrectlyFormedChainOfCommands()
    {
        $this->markTestSkipped();
    }

    // Test it should correctly log when logging is enabled
    public function testItShouldCorrectlyLogWhenLoggingIsEnabled()
    {
        $this->markTestSkipped();
    }

    // Test it should not log where logging is disabled
    public function testItShouldNotLogWhenLoggingIsDisabled()
    {
        $this->markTestSkipped();
    }
}
