# NimiasChainCommandBundle

NimiasChainCommandBundle is a Symfony bundle that implements command chaining functionality.

Other Symfony bundles in the application could register their console commands to be members of a command chain.

When a user runs the main command in a chain, all other commands registered in this chain will be executed as well.

Installation
---------------------------

###Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require nimias/chain-command-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

###Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Nimias\ChainCommandBundle\NimiasChainCommandBundle(),
        );

        // ...
    }

    // ...
}
```

How to use
-------------------------

Please, see [documentation](Resources/doc/index.rst).

ToDo
--------------------------
* Implement tests for \Nimias\ChainCommandBundle\Helper\ConsoleOutputFilterHelper
* Implement tests for \Nimias\ChainCommandBundle\Helper\OutputBufferHelper
* Implement skiped tests in \Nimias\ChainCommandBundle\Tests\Service\CommandEventsSubscriber\ProcessTerminateTest class.
