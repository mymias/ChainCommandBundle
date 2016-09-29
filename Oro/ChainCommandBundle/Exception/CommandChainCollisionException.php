<?php
/*
 * This file is part of the OroChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Oro\ChainCommandBundle\Exception;

/**
 * Exception for adding new command chain members validation process
 *
 * @See \Oro\ChainCommandBundle\Service\CommandChainsRegistry::validate
 */
class CommandChainCollisionException extends \Exception
{}