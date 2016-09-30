NimiasChainCommandBundle
######################

NimiasChainCommandBundle provides functionality:

* Any Symfony bundle in the application could register their console commands to be members of some command chain.
* When a user runs the main command in a chain, all other commands registered in this chain should be executed as well.
* Commands registered as chain members can no longer be executed on their own.

Usage
================

There are two methods to create command chine:

1. Static: specifying chains in config files (in app/config)
2. Dynamic: create new chains and add new member to chains at runtime using service container

Static (in config file)
-------------------------------
Create configuration section named "nimias_chain_command" and fill it as in example:

.. code-block:: yaml

    # NimiasChainCommandBundle Configuration
    nimias_chain_command:
        logging:
            enabled:  true        # true by default
        command_chains:
            server:status:        # main command name
                bar:first-test:   # child command name
                    priority: 10  # priority. Required param. Can be > 0 and < 1000000
                bar:second-test:
                    priority: 10
                    argument: 'string_value'
            foo:second-test:
                bar:second-test:
                    priority: 100
                    argument: 'another_value'



Dynamic (using service container)
-----------------------------------------

Get service ``nimias_chain_command.registry`` from container and use its method ``addSubCommandToChain`` to create a new command
chain or add new command in existing chain.

For example:

.. code-block:: php

    <?php

    namespace Your\BundleName\Service;

    use Nimias\ChainCommandBundle\Service\CommandChainsRegistry;

    class SomeService
    {
        public function __construct(CommandChainsRegistry $commandChainsRegistry)
        {
            ...
            $commandChainsRegistry->addSubCommandToChain(
                'cache:clear',      // main command name
                'your:command',     // your command, that you want to add to chain after cache:clear execution
                10,                 // priority of your command in command chain
                ['someoption' => 'optionValue'] // arguments for your command
        }
    }

.. note::

    Also, as you see in ``nimias_chain_command`` config example, you can turn on/off logging for command chain execution.
