<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
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

namespace Apparat\Object\Application\Repository;

use Apparat\Object\Domain\Repository\AdapterStrategyInterface;
use Apparat\Object\Domain\Repository\InvalidArgumentException;

/**
 * Abstract adapter strategy
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
abstract class AbstractAdapterStrategy implements AdapterStrategyInterface
{
    /**
     * Configuration
     *
     * Example
     *
     * @var array
     */
    protected $_config = null;

    /**
     * Adapter strategy type
     *
     * @var string
     */
    const TYPE = 'abstract';

    /**
     * Adapter strategy constructor
     *
     * @param array $config Adapter strategy configuration
     * @param array $signatureConfigKeys Signature relevant configuration properties
     */
    public function __construct(array $config, array $signatureConfigKeys)
    {
        $this->_config = $config;

        // Build the signature
        $signatureConfig = array_intersect_key($this->_config, array_flip($signatureConfigKeys));
        $signatureConfig['type'] = static::TYPE;
        if (count($signatureConfig) < 2) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid adapter strategy signature configuration "%s"',
                    implode(', ', $signatureConfigKeys)
                ), InvalidArgumentException::INVALID_ADAPTER_STRATEGY_SIGNATURE
            );
        }
    }

    /**
     * Return the adapter strategy type
     *
     * @return string Adapter strategy type
     */
    public function getType()
    {
        return static::TYPE;
    }
}
