<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Test
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

use Apparat\Object\Ports\Object;

/**
 * Processing instruction properties test
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class ProcessingInstructionPropertiesTest extends AbstractRepositoryEnabledTest
{
    /**
     * Example object path
     *
     * @var string
     */
    const ARTICLE_PATH = '/2015/12/21/1-article/1';

    /**
     * Test change by altering processing instructions
     */
    public function testProcessingInstructionChange()
    {
        $object = Object::instance(getenv('REPOSITORY_URL').self::ARTICLE_PATH);
        $this->assertTrue(is_array($object->getPropertyData()));
        $objectUrl = $object->getAbsoluteUrl();
        $objectRevision = $object->getRevision();
        $object->setProcessingInstruction('css', 'other-style.css');
        $object->setProcessingInstruction('css', 'other-style.css'); // Intentionally re-set the same property
        $object->setProcessingInstruction('script.var', 1);
        $object->setProcessingInstruction('script.var', [1, 2]); // Intentionally re-set the same property
        $object->setProcessingInstruction('script.var', [3, 4]); // Intentionally re-set the same property
        $this->assertEquals($objectUrl, $object->getAbsoluteUrl());
        $this->assertEquals($objectRevision->getRevision(), $object->getRevision()->getRevision());
        $this->assertTrue($object->hasBeenModified());
        $this->assertFalse($object->hasBeenMutated());
    }
}