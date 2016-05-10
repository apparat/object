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

namespace Apparat\Object\Domain\Model\Properties;

use Apparat\Object\Domain\Factory\RelationFactory;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Relation\RelationInterface;

/**
 * Object resource relations
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class Relations extends AbstractProperties
{
    /**
     * Collection name
     *
     * @var string
     */
    const COLLECTION = 'relations';
    /**
     * Relations
     *
     * @var array
     */
    protected $relations = [];


    /**
     * Relations constructor
     *
     * @param array $data Property data
     * @param ObjectInterface $object Owner object
     */
    public function __construct(array $data, ObjectInterface $object)
    {
        parent::__construct($data, $object);

        // Run through all registered relation type collections
        /**
         * @var string $relationType
         * @var RelationInterface[] $relations
         */
        foreach ($this->data as $relationType => $relations) {
            // If the relation type collection is invalid or empty
            if (!is_array($relations) || !count($relations)) {
                // TODO Trigger warning
                continue;
            }

            // Run through all (serialized) relations
            foreach ($relations as $serializedRelation) {
                $this->addRelationInstance(RelationFactory::createFromString(
                    $relationType,
                    $serializedRelation,
                    $this->object->getRepositoryPath()->getRepository()
                ));
            }
        }
    }

    /**
     * Add a relation
     *
     * @param RelationInterface $relation Relation
     */
    protected function addRelationInstance(RelationInterface $relation)
    {
        $this->relations[$relation->getSignature()] = $relation;
    }

    /**
     * Unserialize and add a relation
     *
     * @param string|RelationInterface $relation Serialized or instantiated object relation
     * @param string|null $relationType Relation type
     * @return Relations Self reference
     * @throws InvalidArgumentException If the relation is not a valid relation instance
     */
    public function addRelation($relation, $relationType = null)
    {
        // If a new relation is to be added
        $relation = $this->getRelationInstance($relation, $relationType);
        if (!array_key_exists($relation->getSignature(), $this->relations)) {
            $relations = clone $this;
            $relations->addRelationInstance($relation);
            return $relations;
        }

        // Else: Return this
        return $this;
    }

    /**
     * Instantiate a relation
     *
     * @param string|RelationInterface $relation Serialized or instantiated object relation
     * @param string|null $relationType Relation type
     * @return RelationInterface Relation instance
     * @throws InvalidArgumentException If the relation is not a valid relation instance
     */
    public function getRelationInstance($relation, $relationType = null)
    {
        // Unserialize and instantiate the relation if it's given in serialized form
        if (is_string($relation)) {
            // Validate the relation type
            RelationFactory::validateRelationType($relationType);

            // Create the relation instance
            $relation = RelationFactory::createFromString(
                $relationType,
                $relation,
                $this->object->getRepositoryPath()->getRepository()
            );
        }

        // If the relation is not a valid relation instance
        if (!($relation instanceof RelationInterface)) {
            throw new InvalidArgumentException(
                'Invalid object relation',
                InvalidArgumentException::INVALID_OBJECT_RELATION
            );
        }

        return $relation;
    }

    /**
     * Delete an object relation
     *
     * @param RelationInterface $relation Object relation
     * @return Relations Self reference
     */
    public function deleteRelation(RelationInterface $relation)
    {
        // If a new relation is to be added
        if (array_key_exists($relation->getSignature(), $this->relations)) {
            $relations = clone $this;
            unset($relations->relations[$relation->getSignature()]);
            return $relations;
        }

        // Else: Return this
        return $this;
    }

    /**
     * Get all relations (optional: Of a particular type)
     *
     * @param string|null $relationType Optional: Relation type
     * @return RelationInterface[] Relations
     */
    public function getRelations($relationType = null)
    {

        // Return all relations in case no particular type was requested
        if ($relationType === null) {
            return array_values($this->relations);
        }

        // Return all relations matching the requested type
        return $this->findRelations([RelationInterface::FILTER_TYPE => $relationType]);
    }

    /**
     * Find and return particular relations
     *
     * @param array $criteria Relation criteria
     * @return RelationInterface[] Relations
     */
    public function findRelations(array $criteria)
    {
        // Validate the relation type (if given as a criteria)
        if (array_key_exists(RelationInterface::FILTER_TYPE, $criteria)) {
            RelationFactory::validateRelationType($criteria[RelationInterface::FILTER_TYPE]);
        }

        // Find and return the relations matching the criteria
        return array_values(array_filter($this->relations, function (RelationInterface $relation) use ($criteria) {
            foreach ($criteria as $property => $value) {
                switch ($property) {
                    case RelationInterface::FILTER_TYPE:
                        if ($relation->getType() != $value) {
                            return false;
                        }
                        break;
                    case RelationInterface::FILTER_URL:
                        if (strpos($relation->getUrl(), $value) === false) {
                            return false;
                        }
                        break;
                    case RelationInterface::FILTER_LABEL:
                        if (strpos($relation->getLabel(), $value) === false) {
                            return false;
                        }
                        break;
                    case RelationInterface::FILTER_EMAIL:
                        if (strpos($relation->getEmail(), $value) === false) {
                            return false;
                        }
                        break;
                    case RelationInterface::FILTER_COUPLING:
                        if ($relation->getCoupling() !== intval(!!$value)) {
                            return false;
                        }
                        break;
                    default:
                        return false;
                }
            }
            return true;
        }));
    }

    /**
     * Return the property values as array
     *
     * @return array Property values
     */
    public function toArray()
    {
        $relations = [];
        /** @var RelationInterface $relation */
        foreach ($this->relations as $relation) {
            $relationType = $relation->getType();
            if (!array_key_exists($relationType, $relations)) {
                $relations[$relationType] = [strval($relation)];
                continue;
            }
            $relations[$relationType][] = strval($relation);
        }
        ksort($relations);
        return $relations;
    }
}
