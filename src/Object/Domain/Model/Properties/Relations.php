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
        // Initialize the relation type
        if (!array_key_exists($relation->getType(), $this->relations)) {
            $this->relations[$relation->getType()] = [];
        }

        $this->relations[$relation->getType()][$relation->getSignature()] = $relation;
    }

    /**
     * Unserialize and add a relation
     *
     * @param string $relationType Relation type
     * @param string|RelationInterface $relation Serialized or instantiated object relation
     * @return Relations Self reference
     * @throws InvalidArgumentException If the relation is not a valid relation instance
     */
    public function addRelation($relationType, $relation)
    {
        // Unserialize and instantiate the relation if it's given in serialized form
        if (is_string($relation)) {
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

        // If a new relation is to be added
        if (empty($this->relations[$relationType])
            || !array_key_exists($relation->getSignature(), $this->relations[$relationType])
        ) {
            $relations = clone $this;
            $relations->addRelationInstance($relation);
            return $relations;
        }

        // Else: Return this
        return $this;
    }

    /**
     * Get all relations (optional: Of a particular type)
     *
     * @param string|null $relationType Optional: Relation type
     * @return array Object relations
     */
    public function getRelations($relationType = null) {

        // Return all relations in case no particular type was requested
        if ($relationType === null) {
            return $this->relations;
        }

        // Validate the relation type
        RelationFactory::validateRelationType($relationType);

        return empty($this->relations[$relationType]) ? [] : $this->relations[$relationType];
    }

    /**
     * Return the property values as array
     *
     * @return array Property values
     */
    public function toArray()
    {
        $relations = array_filter(
            array_map(
                function(array $relationTypeCollection) {
                    if (!count($relationTypeCollection)) {
                        return false;
                    }
                    return array_values(array_map('strval', $relationTypeCollection));
                },
                $this->relations
            )
        );
        ksort($relations);
        return $relations;
    }
}
