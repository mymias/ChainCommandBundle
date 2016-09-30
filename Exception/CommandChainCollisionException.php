<?php
/*
 * This file is part of the NimiasChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimias\ChainCommandBundle\Exception;

/**
 * Exception for adding new command chain members validation process
 *
 * @See \Nimias\ChainCommandBundle\Service\CommandChainsRegistry::validate
 */
class CommandChainCollisionException extends \Exception
{}