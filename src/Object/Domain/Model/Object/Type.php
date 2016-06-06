<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Object;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Contract\ObjectTypesInterface;
use Apparat\Object\Domain\Contract\SerializablePropertyInterface;
use Apparat\Object\Domain\Contract\TypeServiceInterface;

/**
 * Object type
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Type implements SerializablePropertyInterface, ObjectTypesInterface
{
    /**
     * Object type
     *
     * @var string
     */
    protected $type = null;
    /**
     * Type service
     *
     * @var TypeServiceInterface
     */
    protected $typeService;

    /**
     * Type constructor
     *
     * @param string $type Object type
     * @param TypeServiceInterface $typeService Type service
     * @throws InvalidArgumentException If the type is not supported
     */
    public function __construct($type, TypeServiceInterface $typeService)
    {
        $this->typeService = $typeService;

        // If the type is not supported
        if (!$this->typeService->supportsType($type)) {
            throw new InvalidArgumentException(
                sprintf('Invalid object type "%s"', $type),
                InvalidArgumentException::INVALID_OBJECT_TYPE
            );
        }

        $this->type = $type;
    }

    /**
     * Unserialize the string representation of this property
     *
     * @param string $str Serialized property
     * @return Type Type property
     */
    public static function unserialize($str)
    {
        return Kernel::create(static::class, [$str]);
    }

    /**
     * Serialize the property
     *
     * @return mixed Property serialization
     */
    public function serialize()
    {
        return $this->getType();
    }

    /**
     * Return the object type
     *
     * @return string Object type
     */
    public function getType()
    {
        return $this->type;
    }
}
