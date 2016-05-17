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

namespace Apparat\Object\Application\Factory;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Domain\Model\Properties\SystemProperties;

/**
 * Object factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class ObjectFactory
{
    /**
     * Create an object
     *
     * @param RepositoryPathInterface $path Repository object path
     * @param ResourceInterface $objectResource
     * @return ObjectInterface Object
     * @throws InvalidArgumentException If the object type is undefined
     */
    public static function createFromResource(RepositoryPathInterface $path, ResourceInterface $objectResource)
    {
        $propertyData = $objectResource->getPropertyData();

        // If the object type is undefined
        if (!array_key_exists(SystemProperties::COLLECTION, $propertyData) ||
            !is_array($propertyData[SystemProperties::COLLECTION]) ||
            empty($propertyData[SystemProperties::COLLECTION]['type'])
        ) {
            throw new InvalidArgumentException(
                'Undefined object type',
                InvalidArgumentException::UNDEFINED_OBJECT_TYPE
            );
        }

        // Determine the object class
        $objectClass = self::objectClassFromType($path->getType());

        // Instantiate the object
        return Kernel::create($objectClass, [$path, $objectResource->getPayload(), $propertyData]);
    }

    /**
     * Create and return a new object
     *
     * @param RepositoryPathInterface $path Repository object path
     * @param string $payload Object payload
     * @param array $propertyData Object property data
     * @return ObjectInterface Object
     */
    public static function createFromParams(RepositoryPathInterface $path, $payload = '', array $propertyData = [])
    {
        // Determine the object class
        $objectClass = self::objectClassFromType($path->getType());

        // Prepare the system properties collection
        $systemPropertyData = (empty($propertyData[SystemProperties::COLLECTION]) ||
            !is_array(
                $propertyData[SystemProperties::COLLECTION]
            )) ? [] : $propertyData[SystemProperties::COLLECTION];
        $systemPropertyData[SystemProperties::PROPERTY_ID] = $path->getId()->getId();
        $systemPropertyData[SystemProperties::PROPERTY_TYPE] = $path->getType()->getType();
        $systemPropertyData[SystemProperties::PROPERTY_REVISION] = $path->getRevision()->getRevision();
        $systemPropertyData[SystemProperties::PROPERTY_CREATED] = $path->getCreationDate()->format('U');
        if (empty($systemPropertyData[SystemProperties::PROPERTY_LANGUAGE])) {
            $systemPropertyData[SystemProperties::PROPERTY_LANGUAGE] = getenv('OBJECT_DEFAULT_LANGUAGE');
        }
        $propertyData[SystemProperties::COLLECTION] = $systemPropertyData;

        // Instantiate the object
        return Kernel::create($objectClass, [$path, $payload, $propertyData]);
    }

    /**
     * Determine and validate the object class name from its type
     *
     * @param Type $type Object type
     * @return string Object class name
     * @throws InvalidArgumentException If the object type is invalid
     */
    protected static function objectClassFromType(Type $type)
    {
        // If the object type is invalid
        $objectType = $type->getType();
        $objectClass = 'Apparat\\Object\\Application\\Model\\Object\\'.ucfirst($objectType);
        if (!Type::isValidType($objectType) || !class_exists($objectClass)) {
            throw new InvalidArgumentException(
                sprintf('Invalid object type "%s"', $objectType),
                InvalidArgumentException::INVALID_OBJECT_TYPE
            );
        }

        return $objectClass;
    }
}
