<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure
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

namespace Apparat\Object\Infrastructure\Utilities;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Contract\CommonMarkPayloadProcessorInterface;
use Apparat\Object\Application\Model\Object\AbstractCommonMarkObject;
use Apparat\Object\Ports\Relation;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\DocParser;
use League\CommonMark\DocumentProcessorInterface;
use League\CommonMark\Environment;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Element\Link;

/**
 * CommonMark payload processor
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 */
class CommonMarkPayloadProcessor extends AbstractPayloadProcessor implements
    CommonMarkPayloadProcessorInterface,
    DocumentProcessorInterface
{
    /**
     * Mailto URL scheme
     *
     * @var string
     */
    const SCHEME_MAILTO = 'mailto';
    /**
     * List of refers-to relation URLs
     *
     * @var array
     */
    protected $refersTo;
    /**
     * List of embeds relation URLs
     *
     * @var array
     */
    protected $embeds;
    /**
     * Owning CommonMark object
     *
     * @var AbstractCommonMarkObject
     */
    protected $object;

    /**
     * Process the payload of an object
     *
     * @param string $payload Payload
     * @return string Processed payload
     */
    public function processPayload($payload)
    {
        // Reset all relevant relations
        $this->resetRefersToRelations();
        $this->resetEmbedsRelations();

        $env = Environment::createCommonMarkEnvironment();
        $env->addDocumentProcessor($this);

        // Parse and process the object payload
        /** @var DocParser $docParser */
        $docParser = Kernel::create(DocParser::class, [$env]);
        $docParser->parse($payload);

        return $payload;
    }

    /**
     * Reset the refers-to relations
     */
    protected function resetRefersToRelations()
    {
        $this->refersTo = [];

        // Run through all refers-to relations and delete them
        foreach ($this->object->findRelations([Relation::TYPE => Relation::REFERS_TO]) as $refersToRelation) {
            $this->object->deleteRelation($refersToRelation);
        }
    }

    /**
     * Reset the embeds relations
     */
    protected function resetEmbedsRelations()
    {
        $this->embeds = [];

        // Run through all refers-to relations and delete them
        foreach ($this->object->findRelations([Relation::TYPE => Relation::EMBEDS]) as $embedsRelation) {
            $this->object->deleteRelation($embedsRelation);
        }
    }

    /**
     * Process the CommonMark AST
     *
     * @param Document $document CommonMark AST
     * @return void
     */
    public function processDocument(Document $document)
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            // Process link starts as refers-to relations
            if (($node instanceof Link) && $event->isEntering()) {
                $this->addRefersToRelation(
                    $this->stripFragment($node->getUrl()),
                    empty($node->data['title']) ? null : $node->data['title']
                );
            }

            // Process image starts as embeds relations
            if (($node instanceof Image) && $event->isEntering()) {
                $this->addEmbedsRelation(
                    $this->stripFragment($node->getUrl()),
                    empty($node->data['title']) ? null : $node->data['title']
                );
            }
        }
    }

    /**
     * Add a refers-to relation
     *
     * @param string $url Referred URL
     * @param string $label Label
     */
    protected function addRefersToRelation($url, $label = null)
    {
        if (strlen($url) && !array_key_exists($url, $this->refersTo)) {
            $this->refersTo[$url] = true;
            $this->object->addRelation($this->getRelationString($url, $label), Relation::REFERS_TO);
        }
    }

    /**
     * Create a relation string
     *
     * @param string $url URL
     * @param string $label Label
     * @return string relation string
     */
    protected function getRelationString($url, $label)
    {
        $relationString = (strtolower(parse_url($url, PHP_URL_SCHEME)) == self::SCHEME_MAILTO)
            ? '<'.substr($url, strlen(self::SCHEME_MAILTO) + 1).'>'
            : $url;
        if (!empty($label)) {
            $relationString .= ' '.$label;
        }
        return $relationString;
    }

    /**
     * Strip off the fragment of an URL
     *
     * @param string $url URL
     * @return string URL with fragmet stripped
     */
    protected function stripFragment($url)
    {
        $fragment = parse_url($url, PHP_URL_FRAGMENT);
        if (!empty($fragment) && (substr($url, -strlen($fragment) - 1) == '#'.$fragment)) {
            $url = substr($url, 0, -strlen($fragment) - 1);
        }
        return $url;
    }

    /**
     * Add an embeds relation
     *
     * @param string $url Embedded URL
     * @param string $label Label
     */
    protected function addEmbedsRelation($url, $label = null)
    {
        if (strlen($url) && !array_key_exists($url, $this->embeds)) {
            $this->embeds[$url] = true;
            $this->object->addRelation($this->getRelationString($url, $label), Relation::EMBEDS);
        }
    }
}
