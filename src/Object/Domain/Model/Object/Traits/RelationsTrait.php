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

namespace Apparat\Object\Domain\Model\Object\Traits;
use Apparat\Object\Domain\Model\Properties\Relations;

/**
 * Relations trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 * @property array $collectionStates
 */
trait RelationsTrait
{
    /**
     * Object relations
     *
     * @var Relations
     */
    protected $relations;

    /**
     * Set the relations collection
     *
     * @param Relations $relations Relations collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    protected function setRelations(Relations $relations, $overwrite = false)
    {
        $this->relations = $relations;
        $relationsState = spl_object_hash($this->relations);

        // If the domain property collection state has changed
        if (!$overwrite
            && !empty($this->collectionStates[Relations::COLLECTION])
            && ($relationsState !== $this->collectionStates[Relations::COLLECTION])
        ) {
            // Flag this object as dirty
            $this->setDirtyState();
        }

        $this->collectionStates[Relations::COLLECTION] = $relationsState;
    }

    /**
     * Set the object state to dirty
     */
    abstract protected function setDirtyState();
}
