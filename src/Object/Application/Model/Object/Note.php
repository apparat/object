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

namespace Apparat\Object\Application\Model\Object;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Contract\ObjectTypesInterface;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;

/**
 * Note object
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class Note extends AbstractCommonMarkObject
{
    /**
     * Object type
     *
     * @var string
     */
    const TYPE = ObjectTypesInterface::NOTE;
    /**
     * Domain property collection class
     *
     * @var string
     */
    protected $domainPropertyCClass = \Apparat\Object\Application\Model\Properties\Domain\Note::class;

    /**
     * Set the payload
     *
     * @param string $payload Payload
     * @return Note Self reference
     */
    public function setPayload($payload)
    {
        // Get the current title and abstract and determine if they should be adapted
        list($currentTitle, $currentAbstract) = $this->extractTitleAndAbstract($this->getPayload());
        $adaptTitle = $currentTitle == $this->getTitle();
        $adaptAbstract = $currentAbstract == $this->getAbstract();

        // Set and process the payload
        parent::setPayload($payload);

        // Get the new title and abstract
        list($title, $abstract) = $this->extractTitleAndAbstract($this->getPayload());

        // If the title should be adapted
        if ($adaptTitle) {
            $this->setTitle($title);
        }

        // If the abstract should be adapted
        if ($adaptAbstract) {
            $this->setAbstract($abstract);
        }

        return $this;
    }

    /**
     * Extract the title and abstract out of the payload
     *
     * @param string $markdownPayload Markdown payload
     * @return array Titel and abstract
     */
    protected function extractTitleAndAbstract($markdownPayload)
    {
        $abstract = trim($markdownPayload);
        if (preg_match('%^(.+?)\R%', $markdownPayload, $firstParagraph)) {
            $abstract = trim($firstParagraph[1]);
        }

        // Strip formatting
        if (strlen($abstract)) {
            $environment = Environment::createCommonMarkEnvironment();
            /** @var CommonMarkConverter $converter */
            $converter = Kernel::create(CommonMarkConverter::class, [[], $environment]);
            $abstract = trim(strip_tags($converter->convertToHtml($abstract)));
        }

        $title = $abstract;
        return [$title, $abstract];
    }
}
