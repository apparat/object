<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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

use Apparat\Object\Domain\Model\Author\AuthorInterface;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Domain\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Domain\Model\Properties\InvalidArgumentException as PropertyInvalidArgumentException;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Model\Properties\ProcessingInstructions;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Properties\SystemProperties;

/**
 * Abstract object
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
abstract class AbstractObject implements ObjectInterface
{
    /**
     * System properties
     *
     * @var SystemProperties
     */
    protected $_systemProperties;
    /**
     * Meta properties
     *
     * @var MetaProperties
     */
    protected $_metaProperties;
    /**
     * Domain properties
     *
     * @var AbstractDomainProperties
     */
    protected $_domainProperties;
    /**
     * Object relations
     *
     * @var Relations
     */
    private $_relations;
    /**
     * Processing instructions
     *
     * @var ProcessingInstructions
     */
    private $_processingInstructions;
    /**
     * Object payload
     *
     * @var string
     */
    protected $_payload;
    /**
     * Repository path
     *
     * @var RepositoryPathInterface
     */
    protected $_path;
    /**
     * Domain property collection class
     *
     * @var string
     */
    protected $_domainPropertyCollectionClass = null;

    /**
     * Object constructor
     *
     * @param RepositoryPathInterface $path Object repository path
     * @param array $propertyData Property data
     * @param string $payload Object payload
     * @throws PropertyInvalidArgumentException If the domain property collection class is invalid
     */
    public function __construct(RepositoryPathInterface $path, array $propertyData = [], $payload = '')
    {
        // If the domain property collection class is invalid
        if (!is_subclass_of($this->_domainPropertyCollectionClass, AbstractDomainProperties::class)) {
            throw new PropertyInvalidArgumentException(
                sprintf(
                    'Invalid domain property collection class "%s"',
                    $this->_domainPropertyCollectionClass
                ),
                PropertyInvalidArgumentException::INVALID_DOMAIN_PROPERTY_COLLECTION_CLASS
            );
        }

        $this->_payload = $payload;
        $this->_path = $path;

        // Instantiate the system properties
        $systemPropertyData = (empty($propertyData[SystemProperties::COLLECTION]) || !is_array(
                $propertyData[SystemProperties::COLLECTION]
            )) ? [] : $propertyData[SystemProperties::COLLECTION];
        $this->_systemProperties = new SystemProperties($systemPropertyData, $this);

        // Instantiate the meta properties
        $metaPropertyData = (empty($propertyData[MetaProperties::COLLECTION]) || !is_array(
                $propertyData[MetaProperties::COLLECTION]
            )) ? [] : $propertyData[MetaProperties::COLLECTION];
        $this->_metaProperties = new MetaProperties($metaPropertyData, $this);

        // Instantiate the domain properties
        $domainPropertyData = (empty($propertyData[AbstractDomainProperties::COLLECTION]) || !is_array(
                $propertyData[AbstractDomainProperties::COLLECTION]
            )) ? [] : $propertyData[AbstractDomainProperties::COLLECTION];
        $this->_domainProperties = new $this->_domainPropertyCollectionClass($domainPropertyData, $this);

        // Instantiate the processing instructions
        $processingInstructionData = (empty($propertyData[ProcessingInstructions::COLLECTION]) || !is_array(
                $propertyData[ProcessingInstructions::COLLECTION]
            )) ? [] : $propertyData[ProcessingInstructions::COLLECTION];
        $this->_processingInstructions = new ProcessingInstructions($processingInstructionData, $this);

        // Instantiate the object relations
        $relationData = (empty($propertyData[Relations::COLLECTION]) || !is_array(
                $propertyData[Relations::COLLECTION]
            )) ? [] : $propertyData[Relations::COLLECTION];
        $this->_relations = new Relations($relationData, $this);
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->_systemProperties->getId();
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getType()
    {
        return $this->_systemProperties->getType();
    }

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->_systemProperties->getRevision();
    }

    /**
     * Return the creation date & time
     *
     * @return \DateTimeImmutable Creation date & time
     */
    public function getCreated()
    {
        return $this->_systemProperties->getCreated();
    }

    /**
     * Return the publication date & time
     *
     * @return \DateTimeImmutable Publication date & time
     */
    public function getPublished()
    {
        return $this->_systemProperties->getPublished();
    }

    /**
     * Return all object keywords
     *
     * @return array Object keywords
     */
    public function getKeywords()
    {
        return $this->_metaProperties->getKeywords();
    }

    /**
     * Return all object categories
     *
     * @return array Object categories
     */
    public function getCategories()
    {
        return $this->_metaProperties->getCategories();
    }

    /**
     * Return all object authors
     *
     * @return AuthorInterface[] Authors
     */
    public function getAuthors()
    {
        return $this->_metaProperties->getAuthors();
    }

    /**
     * Add an object author
     *
     * @param AuthorInterface $author Author
     * @return ObjectInterface Self reference
     */
    public function addAuthor(AuthorInterface $author)
    {
        $authors = $this->_metaProperties->getAuthors();
        $authors[] = $author;
        $this->_metaProperties->setAuthors($authors);
        return $this;
    }

    /**
     * Return the object repository path
     *
     * @return RepositoryPathInterface Object repository path
     */
    public function getRepositoryPath()
    {
        return $this->_path;
    }

    /**
     * Return the absolute object URL
     *
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return getenv('APPARAT_BASE_URL').$this->_path->getRepository()->getUrl().strval($this->_path);
    }
}
