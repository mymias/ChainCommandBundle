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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Tests for CommandEventsSubscriber::processCommand() method
 *
 * @See \Nimias\ChainCommandBundle\Service\CommandEventsSubscriber::processCommand
 */
class ProcessCommandTest extends \PHPUnit_Framework_TestCase
{
    use SetUpTrait;

    // Test it should prohibit execute child command independently
    /**
     * @param $childCommandName
     *
     * @dataProvider dataProviderValidChildCommandNames
     */
    public function testItShouldProhibitExecuteChildCommandIndependently($childCommandName)
    {
        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($childCommandName);

        $output = new BufferedOutput();

        $event = $this->createMock(ConsoleCommandEvent::class);
        $event->method('getCommand')->willReturn($command);
        $event->method('getOutput')->willReturn($output);

        $result = $this->commandSubscriber->processCommand($event);

        $expected = sprintf(sprintf(
            "Error: %s command is a member of %s command chain and cannot be executed on its own.\n",
            $childCommandName,
            $this->firstCommandName
        ));
        $actual = $output->fetch();

        $this->assertEquals($expected, $actual);
    }

    // Test it should correctly log actions where logging is enabled
    public function testItShouldCorrectlyLogActionsWhenLoggingIsEnabled()
    {
        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($this->firstCommandName);

        $event = $this->createMock(ConsoleCommandEvent::class);
        $event->method('getCommand')->willReturn($command);

        $this->commandSubscriber->setLoggingEnabled(true);

        $result = $this->commandSubscriber->processCommand($event);

        $expected = [
            [sprintf(
                '%s is a master command of a command chain that has registered member commands',
                $this->firstCommandName
            ), []],
            [sprintf(
                '%s registered as a member of %s command chain',
                $this->secondCommandName,
                $this->firstCommandName
            ), []],
            [sprintf(
                '%s registered as a member of %s command chain',
                $this->fourthCommandName,
                $this->firstCommandName
            ), []],
            [sprintf(
                '%s registered as a member of %s command chain',
                $this->thirdCommandName,
                $this->firstCommandName
            ), []],
            [sprintf(
                'Executing %s command itself first:',
                $this->firstCommandName
            ), []],
        ];
        $actual = $this->logInfo;

        $this->assertEquals($expected, $actual);
    }

    // Test it should not log actions where logging is disabled
    public function testItShouldNotLogActionsWhenLoggingIsDisabled()
    {
        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($this->firstCommandName);

        $event = $this->createMock(ConsoleCommandEvent::class);
        $event->method('getCommand')->willReturn($command);

        $this->commandSubscriber->setLoggingEnabled(false);

        $result = $this->commandSubscriber->processCommand($event);

        $expected = [];
        $actual = $this->logInfo;

        $this->assertEquals($expected, $actual);
    }

    public function dataProviderValidChildCommandNames()
    {
        return [
            [$this->secondCommandName],
            [$this->thirdCommandName],
            [$this->fourthCommandName],
        ];
    }
}
