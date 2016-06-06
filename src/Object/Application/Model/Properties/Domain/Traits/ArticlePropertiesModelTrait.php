<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Apparat\Object\Application\Model\Properties\Domain\Traits
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
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

namespace Apparat\Object\Application\Model\Properties\Domain\Traits;

use Apparat\Object\Application\Model\Properties\Datatype\ApparatUrl;
use Apparat\Object\Application\Model\Properties\Datatype\Sentence;
use Apparat\Object\Application\Model\Properties\Datatype\Text;
use Apparat\Object\Application\Model\Properties\Datatype\Token;
use Apparat\Object\Application\Model\Properties\Datatype\Url;
use Apparat\Object\Domain\Contract\ObjectTypesInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Article properties model trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 * @method ObjectInterface getObject()
 */
trait ArticlePropertiesModelTrait
{
    /**
     * Property model: Location
     *
     * @var array
     */
    protected $pmLocation = [
        false,
        [ApparatUrl::class, Sentence::class],
        [ApparatUrl::class => [ObjectTypesInterface::ADDRESS, ObjectTypesInterface::GEO]]
    ];
    /**
     * Property model: RSVP
     *
     * @var array
     */
    protected $pmRsvp = [false, [Token::class]];
    /**
     * Property model: Comment
     *
     * @var array
     */
    protected $pmComment = [
        true,
        [ApparatUrl::class, Text::class],
        [ApparatUrl::class => [ObjectTypesInterface::CITE]]
    ];
    /**
     * Property model: Featured
     *
     * @var array
     */
    protected $pmFeatured = [
        false,
        [ApparatUrl::class, Url::class],
        [ApparatUrl::class => [ObjectTypesInterface::IMAGE]]
    ];
}
