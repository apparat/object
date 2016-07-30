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

use Apparat\Object\Application\Model\Object\Note;
use Apparat\Object\Ports\Types\Object as ObjectTypes;

/**
 * Note object tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class NoteObjectTest extends AbstractObjectTest
{
    /**
     * Test the creation and persisting of an image object
     */
    public function testCreateAndPublishNoteObject()
    {
        // Create a temporary repository
        $tempRepoDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo';
        $payload = "This is a sample **note object**. It features:\n\n";
        $payload .= "* Multiple sencentes / lines\n* A simple list";
        $creationDate = new \DateTimeImmutable('yesterday');
        $note = $this->createRepositoryAndNoteObject($tempRepoDirectory, $payload, $creationDate);
        $this->assertInstanceOf(Note::class, $note);
        $this->assertEquals($payload, $note->getPayload());
        $this->assertFileExists($tempRepoDirectory.
            str_replace('/', DIRECTORY_SEPARATOR, $note->getRepositoryLocator()
                ->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))));
        $this->assertEquals($creationDate, $note->getCreated());
        $this->assertEquals('This is a sample note object. It features:', $note->getTitle());

        // Delete temporary repository
        $this->deleteRecursive($tempRepoDirectory);
    }

    /**
     * Create a temporary repository and a note object
     *
     * @param string $tempRepoDirectory Repository directory
     * @param string $payload Note payload
     * @param \DateTimeInterface $creationDate Note creation date
     * @return Note Note object
     */
    protected function createRepositoryAndNoteObject(
        $tempRepoDirectory,
        $payload,
        \DateTimeInterface $creationDate = null
    ) {
        $fileRepository = $this->createRepository($tempRepoDirectory);

        // Create a new note in the temporary repository
        return $fileRepository->createObject(ObjectTypes::NOTE, $payload, [], $creationDate);
    }
}
