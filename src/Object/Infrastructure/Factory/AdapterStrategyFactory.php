<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Apparat\Object\Infrastructure\Factory;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Repository\AdapterStrategyFactoryInterface;
use Apparat\Object\Domain\Repository\AdapterStrategyInterface;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;

/**
 * Repository adapter strategy factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 */
class AdapterStrategyFactory implements AdapterStrategyFactoryInterface
{
    /**
     * Known adapter strategy types
     *
     * @var array
     */
    protected static $types = [
        FileAdapterStrategy::TYPE => FileAdapterStrategy::class,
    ];

    /**
     * Add an adapter strategy
     *
     * @param string $type Adapter type
     * @param string $class Adapter class
     * @throws InvalidArgumentException If the type is invalid
     * @throws InvalidArgumentException If the class is invalid
     */
    public static function setAdapterStrategyTypeClass($type, $class)
    {

        // If the type is invalid
        if (!strlen($type)) {
            throw new InvalidArgumentException(
                sprintf('Invalid adapter strategy type "%s"', $type),
                InvalidArgumentException::INVALID_ADAPTER_STRATEGY_TYPE
            );
        }

        // If the class doesn't exist or is invalid
        if (!class_exists($class) ||
            !(new \ReflectionClass($class))->implementsInterface(AdapterStrategyInterface::class)
        ) {
            throw new InvalidArgumentException(
                sprintf('Invalid adapter strategy class "%s"', $class),
                InvalidArgumentException::INVALID_ADAPTER_STRATEGY_CLASS
            );
        }

        self::$types[$type] = $class;
    }

    /**
     * Instantiate and return an adapter strategy
     *
     * @param array $config Adapter strategy config
     * @return AdapterStrategyInterface Repository adapter
     * @throws InvalidArgumentException If the adapter strategy config is empty
     * @throws InvalidArgumentException If the adapter strategy type is missing or invalid
     */
    public function createFromConfig(array $config)
    {
        // If the adapter strategy config is empty
        if (!count($config)) {
            throw new InvalidArgumentException(
                'Empty adapter strategy configuration',
                InvalidArgumentException::EMPTY_ADAPTER_STRATEGY_CONFIG
            );
        }

        // If the adapter strategy type is missing or invalid
        if (empty($config['type']) || !array_key_exists($config['type'], self::$types)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid adapter strategy type "%s"',
                    empty($config['type']) ? '(empty)' : $config['type']
                ),
                InvalidArgumentException::INVALID_ADAPTER_STRATEGY_TYPE
            );
        }

        // Instantiate the adapter strategy
        return Kernel::create(self::$types[$config['type']], [$config]);
    }
}
