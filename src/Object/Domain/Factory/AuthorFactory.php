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

namespace Apparat\Object\Domain\Factory;

use Apparat\Object\Domain\Model\Author\ApparatAuthor;
use Apparat\Object\Domain\Model\Author\AuthorInterface;
use Apparat\Object\Domain\Model\Author\GenericAuthor;
use Apparat\Object\Domain\Model\Author\InvalidArgumentException;
use Apparat\Object\Domain\Model\Author\InvalidAuthor;
use Apparat\Object\Domain\Model\Path\ApparatInvalidArgumentException;
use Apparat\Object\Domain\Model\Path\ApparatUrl;
use Apparat\Object\Domain\Repository\RepositoryInterface;

/**
 * Author factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class AuthorFactory
{
    /**
     * Parse and instantiate an author serialization
     *
     * @param string $author Author serialization
     * @param RepositoryInterface $contextRepository Context repository
     * @return AuthorInterface Object author
     * @throws InvalidArgumentException If the author format is invalid
     */
    public static function createFromString($author, RepositoryInterface $contextRepository = null)
    {
        // Try to instantiate an apparat object based author
        try {
            $apparatUrl = new ApparatUrl($author, true, $contextRepository);
            return new ApparatAuthor($apparatUrl);

            // If there's an apparat URL problem
        } catch (ApparatInvalidArgumentException $e) {
            return new InvalidAuthor($author, $e);

            // Proceed on other errors
        } catch (\Apparat\Object\Domain\Model\Path\InvalidArgumentException $e) {
        }

        // Try to instantiate a generic author
        try {
            return GenericAuthor::unserialize($author);

            // Proceed on errors
        } catch (InvalidArgumentException $e) {
        }

        throw new InvalidArgumentException(
            sprintf('Invalid author format "%s"', $author),
            InvalidArgumentException::INVALID_AUTHOR_FORMAT
        );
    }
}
