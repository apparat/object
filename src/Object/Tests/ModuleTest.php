<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Tests
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

namespace Apparat\Object\Tests;

use Apparat\Object\Ports\Types\Object as ObjectTypes;
use Apparat\Resource\Module;

/**
 * Module tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class ModuleTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Test the module's auto-run feature
     */
    public function testModuleAutorun()
    {
        include dirname(__DIR__).DIRECTORY_SEPARATOR.'Autorun.php';
        $this->assertEquals(Module::NAME, (new Module())->getName());
    }

    /**
     * Test enabling an object type
     */
    public function testEnableObjectType()
    {
        $supportedObjectTypes = ObjectTypes::getSupportedTypes();
        ObjectTypes::enableType(ObjectTypes::EVENT);
        $this->assertEquals(
            array_merge($supportedObjectTypes, [ObjectTypes::EVENT => ObjectTypes::EVENT]),
            ObjectTypes::getSupportedTypes()
        );
        $this->assertTrue(ObjectTypes::supportsType(ObjectTypes::EVENT));
        $this->assertFalse(ObjectTypes::supportsType('invalid'));
    }

    /**
     * Test enabling an invalid object type
     *
     * @expectedException \Apparat\Object\Application\Service\OutOfBoundsException
     * @expectedExceptionCode 1464810106
     */
    public function testEnableInvalidType()
    {
        ObjectTypes::enableType('invalid');
    }
}
