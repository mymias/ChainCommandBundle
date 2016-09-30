<?php
/*
 * This file is part of the NimiasChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimias\ChainCommandBundle\Tests\Service;

use Nimias\ChainCommandBundle\Service\CommandChainsRegistry;

/**
 * Test class for CommandChainsRegistry service
 *
 * @See \Nimias\ChainCommandBundle\Service\CommandChainsRegistry
 */
class CommandChainsRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommandChainsRegistry
     */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->registry = new CommandChainsRegistry();
    }

    // Validation tests
        // It should not take the main command, that already set up as child command
    public function test_It_Should_Not_Take_Main_Command_That_Already_Set_Up_As_Child()
    {
        $this->expectException('\Nimias\ChainCommandBundle\Exception\CommandChainCollisionException');

        $mainCommandName = 'dfadfd.adfadf';
        $someAnotherMainCommandName = 'akghalkhg.adsfaihg';
        $childCommandName = 'fadggkjakdglhgl.asdhaighaoigh';

        // Set up main command as child
        $this->registry->addSubCommandToChain($someAnotherMainCommandName, $mainCommandName, 10);

        // try to add main command as main. Should throw an exception
        $this->registry->addSubCommandToChain($mainCommandName, $childCommandName, 10);
    }

        // It should not take child commands, that already set up as main commands
    public function test_It_Should_Not_Take_Child_Commands_That_Already_Set_Up_As_Main()
    {
        $this->expectException('\Nimias\ChainCommandBundle\Exception\CommandChainCollisionException');

        $mainCommandName = 'dfadfd.adfadf';
        $childCommandName = 'fadggkjakdglhgl.asdhaighaoigh';
        $someAnotherChildCommandName = 'akghalkhg.adsfaihg';

        // Set up child command as main
        $this->registry->addSubCommandToChain($childCommandName, $someAnotherChildCommandName, 10);

        // try to add child command as child. Should throw an exception
        $this->registry->addSubCommandToChain($mainCommandName, $childCommandName, 10);
    }

        // It should not take an out of range priority param
    /**
     * @param $priority int
     *
     * @dataProvider dataProviderPriority
     */
    public function test_It_Should_Not_Take_An_Out_Of_Range_Priority_Param($priority)
    {
        $this->expectException('\InvalidArgumentException');

        $mainCommandName = 'dfadfd.adfadf';
        $childCommandName = 'fadggkjakdglhgl.asdhaighaoigh';

        // Set up commands with invalid priority
        $this->registry->addSubCommandToChain($mainCommandName, $childCommandName, $priority);
    }

        // It should not take not scalar arguments for child commands
    /**
     * @param $arguments
     *
     * @dataProvider dataProviderArguments
     */
    public function test_It_Should_Not_Take_Non_Scalar_Arguments_For_Child_Commands($arguments)
    {
        $this->expectException('\InvalidArgumentException');

        $mainCommandName = 'dfadfd.adfadf';
        $childCommandName = 'fadggkjakdglhgl.asdhaighaoigh';

        // Set up commands with invalid priority
        $this->registry->addSubCommandToChain($mainCommandName, $childCommandName, 10, $arguments);
    }

    // Positive test
        // It should properly populate data variables with valid data
    /**
     * @param $mainCommandName
     * @param $childCommandName
     * @param $priority
     * @param $arguments
     *
     * @dataProvider dataProviderValid
     */
    public function test_It_Should_Correct_Populate_Data_Variables_With_Valid_Data(
        $mainCommandName,
        $childCommandName,
        $priority,
        $arguments
    )
    {
        // Set up commands
        $this->registry->addSubCommandToChain($mainCommandName, $childCommandName, $priority, $arguments);

        $mainCommandChain = $this->registry->getCommandChain($mainCommandName);

        $this->assertNotEmpty($mainCommandChain);
        $this->assertEquals($arguments, $mainCommandChain[$priority][$childCommandName]);
        $this->assertNotNull($this->registry->getParentCommandName($childCommandName));
    }

    /**
     * Test it should sort data set by priority
     */
    public function test_It_Should_Sort_Data_Set_By_Priority()
    {

    }

    // Negative test
       // It should not yield results in response to requests for non existing data
    /**
     * @param $mainCommandName
     * @param $childCommandName
     * @param $priority
     * @param $arguments
     *
     * @dataProvider dataProviderValid
     */
    public function test_It_Should_Not_Yield_Results_For_Requests_Of_Non_Existing_Data(
        $mainCommandName,
        $childCommandName,
        $priority,
        $arguments
    )
    {
        $fakeMainCommandName = 'dfadsfasdgasdga.adsfa';
        $fakeChildCommandName = 'asjgkuagdfugaskd.adsfad';

        // Set up commands
        $this->registry->addSubCommandToChain($mainCommandName, $childCommandName, $priority, $arguments);

        $mainCommandChain = $this->registry->getCommandChain($fakeMainCommandName);

        $this->assertEmpty($mainCommandChain);
        $this->assertNull($this->registry->getParentCommandName($fakeChildCommandName));
    }

    // DataProviders

    public function dataProviderPriority()
    {
        return [
            [-1],
            [-5],
            [100000000],
        ];
    }

    public function dataProviderArguments()
    {
        return [
            [[
                'a' => 'a',
                2 => [],
            ]],
            [[1 => new \stdClass() ]],
            [[[]]],
        ];
    }

    public function dataProviderValid()
    {
        return [
            ['some.main.command.name', 'some.child.command.name', 10, ['agrname' => 11, 'second' => 'str']],
            ['new.main.name', 'some.new.child.command.name', 10, ['newagrname' => 1111, 'newsecond' => false]],
            ['some.t.main.command.name', 'some.t.child.command.name', 10, ['tagrname' => 'str', 'tsecond' => 24]],
        ];
    }
}
