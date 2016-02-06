<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Repository;

/**
 * Repository invalid argument exception
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * Invalid repository selector
     *
     * @var int
     */
    const INVALID_REPOSITORY_SELECTOR = 1449961609;
    /**
     * Invalid repository selector component
     *
     * @var int
     */
    const INVALID_REPOSITORY_SELECTOR_COMPONENT = 1449999646;
    /**
     * Invalid adapter strategy signature configuration
     *
     * @var int
     */
    const INVALID_ADAPTER_STRATEGY_SIGNATURE = 1450136346;
    /**
     * Invalid apparat base URL
     *
     * @var string
     */
    const INVALID_APPARAT_BASE_URL = 1451162015;
    /**
     * Unknown repository URL
     *
     * @var int
     */
    const UNKNOWN_REPOSITORY_URL = 1451771889;
    /**
     * Invalid repository URL
     *
     * @var int
     */
    const INVALID_REPOSITORY_URL = 1453097878;
    /**
     * Invalid argument name
     *
     * @var string
     */
    protected $_argumentName = null;

    /**
     * Exception constructor
     *
     * @param string $message Exception message
     * @param string $code Exception code
     * @param \Exception|null $previous Previous exception
     * @param null $argumentName Invalid argument name
     */
    public function __construct($message = '', $code = '', \Exception $previous = null, $argumentName = null)
    {
        parent::__construct($message, $code, $previous);
        $this->_argumentName = $argumentName;
    }

    /**
     * Return the invalid argument name
     *
     * @return string
     */
    public function getArgumentName()
    {
        return $this->_argumentName;
    }
}
