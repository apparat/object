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

use Apparat\Object\Application\Model\Object\Article;
use Apparat\Object\Application\Model\Object\Contact;
use Apparat\Object\Ports\Factory\SelectorFactory;
use Apparat\Object\Domain\Model\Object\Collection;
use Apparat\Object\Domain\Model\Uri\RepositoryLocator;

/**
 * Object collection tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class CollectionTest extends AbstractRepositoryEnabledTest
{
    /**
     * Test an object collection
     *
     * @expectedException \Apparat\Object\Domain\Model\Object\RuntimeException
     * @expectedExceptionCode 1456530074
     */
    public function testObjectCollection()
    {
        $selector = SelectorFactory::createFromString('/2015/*/*/*-article');
        $collection = self::$repository->findObjects($selector);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertTrue(is_int(count($collection)));
        $this->assertTrue(count($collection) == 1);

        $uid = 1;
        foreach ($collection as $uid => $article) {
            $this->assertInstanceOf(Article::class, $article);
            $this->assertEquals($uid, $article->getId()->getId());
        }

        $this->assertTrue(isset($collection[$uid]));
        $this->assertInstanceOf(Article::class, $collection[$uid]);

        // Load a contact object
        $contactObjectLocator = new RepositoryLocator(self::$repository, '/2016/01/08/2-contact/2');
        $contactObject = self::$repository->loadObject($contactObjectLocator);
        $this->assertInstanceOf(Contact::class, $contactObject);
        $collection[$uid] = $contactObject;
    }

    /**
     * Test unsetting an object from a collection by index
     *
     * @expectedException \Apparat\Object\Domain\Model\Object\RuntimeException
     * @expectedExceptionCode 1456530074
     */
    public function testObjectCollectionUnset()
    {
        $selector = SelectorFactory::createFromString('/2015/*/*/*-article');
        $collection = self::$repository->findObjects($selector);
        unset($collection[0]);
    }

    /**
     * Test adding an object to a collection
     *
     * @expectedException \Apparat\Object\Domain\Model\Object\InvalidArgumentException
     * @expectedExceptionCode 1450131914
     */
    public function testObjectCollectionAdd()
    {
        // Load a contact object
        $contactObjectLocator = new RepositoryLocator(self::$repository, '/2016/01/08/2-contact/2');
        $contactObject = self::$repository->loadObject($contactObjectLocator);
        $this->assertInstanceOf(Contact::class, $contactObject);

        // Load a collection
        $selector = SelectorFactory::createFromString('/2015/*/*/*-article');
        $collection = self::$repository->findObjects($selector);
        $this->assertInstanceOf(Collection::class, $collection);
        $articleCount = count($collection);

        // Add the contact to the collection
        $collection = $collection->add($contactObject);
        $this->assertEquals($articleCount + 1, count($collection));

        // Add another object by repository locator
        /** @var Collection $collection */
        $collection = $collection->add(new RepositoryLocator(self::$repository, '/2016/02/07/3-article/3'));
        $this->assertEquals($articleCount + 2, count($collection));

        // Add invalid object
        $collection->add(null);
    }

    /**
     * Test removing an object from a collection
     *
     * @expectedException \Apparat\Object\Domain\Model\Object\InvalidArgumentException
     * @expectedExceptionCode 1448737190
     */
    public function testObjectCollectionRemove()
    {
        // Load a contact object
        $contactObjectLocator = new RepositoryLocator(self::$repository, '/2016/01/08/2-contact/2');
        $contactObject = self::$repository->loadObject($contactObjectLocator);
        $this->assertInstanceOf(Contact::class, $contactObject);

        // Load a collection
        $selector = SelectorFactory::createFromString('/2015/*/*/*-article');
        $collection = self::$repository->findObjects($selector);
        $this->assertInstanceOf(Collection::class, $collection);
        $articleCount = count($collection);

        $article = null;
        foreach ($collection as $article) {
            break;
        }

        if ($article instanceof Article) {
            // Add the contact to the collection
            /** @var Collection $collection */
            $collection = $collection->add($contactObject);
            $this->assertEquals($articleCount + 1, count($collection));

            // Remove the article
            $collection = $collection->remove($article);
            $this->assertEquals($articleCount, count($collection));

            // Remove the contact by ID
            $collection = $collection->remove(2);
            $this->assertEquals($articleCount - 1, count($collection));

            // Remove invalid object
            $collection->remove('invalid');
        }
    }

    /**
     * Test appending two object collections
     */
    public function testObjectCollectionAppend()
    {
        // Load an article collection
        $articles = self::$repository->findObjects(SelectorFactory::createFromString('/2015/*/*/*-article'));
        $articleCount = count($articles);
        $article = null;
        foreach ($articles as $article) {
            break;
        }

        // Load a contact collection
        $contacts = self::$repository->findObjects(SelectorFactory::createFromString('/2016/01'));
        $contactCount = count($contacts);
        $contact = null;
        foreach ($contacts as $contact) {
            break;
        }

        // Append the contacts collection to the articles collection
        $combined = $articles->append($contacts);
        $this->assertEquals($articleCount + $contactCount, count($combined));
        $this->assertInstanceOf(Article::class, $combined[$article->getId()->getId()]);
        $this->assertInstanceOf(Contact::class, $combined[$contact->getId()->getId()]);
    }
}
