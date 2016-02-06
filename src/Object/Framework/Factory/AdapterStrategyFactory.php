<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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

namespace Apparat\Object\Framework\Factory;

use Apparat\Object\Domain\Repository\AdapterStrategyFactoryInterface;
use Apparat\Object\Domain\Repository\AdapterStrategyInterface;
use Apparat\Object\Framework\Repository\FileAdapterStrategy;
use Apparat\Object\Framework\Repository\HttpAdapterStrategy;

/**
 * Repository adapter strategy factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Framework
 */
class AdapterStrategyFactory implements AdapterStrategyFactoryInterface
{
    /**
     * Known adapter strategy types
     *
     * @var array
     */
    protected static $_types = [
        FileAdapterStrategy::TYPE => FileAdapterStrategy::class,
        HttpAdapterStrategy::TYPE => HttpAdapterStrategy::class,
    ];

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
        if (empty($config['type']) || !array_key_exists($config['type'], self::$_types)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid adapter strategy type "%s"',
                    empty($config['type']) ? '(empty)' : $config['type']
                ),
                InvalidArgumentException::INVALID_ADAPTER_STRATEGY_TYPE
            );
        }

        // Instantiate the adapter strategy
        return new self::$_types[$config['type']]($config);
    }
}
