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

use Apparat\Object\Domain\Model\Properties\SystemProperties;

/**
 * Object states trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain\Model\Object\Traits
 * @property SystemProperties $systemProperties
 */
trait StatesTrait
{
    /**
     * Object state
     *
     * @var int
     */
    protected $state = self::STATE_CLEAN;
    /**
     * Property collection states
     *
     * @var array
     */
    protected $collectionStates = [];

    /**
     * Return whether the object is in mutated state
     *
     * @return boolean Mutated state
     */
    public function hasBeenMutated()
    {
        return !!($this->state & self::STATE_MUTATED);
    }

    /**
     * Return whether the object is in published state
     *
     * @return boolean Published state
     */
    public function isPublished()
    {
        return $this->systemProperties->isPublished();
    }

    /**
     * Return whether the object has been deleted
     *
     * @return boolean Object is deleted
     */
    public function isDeleted()
    {
        return $this->systemProperties->isDeleted();
    }

    /**
     * Return whether the object has just been deleted
     *
     * @return boolean Object has just been deleted
     */
    public function hasBeenDeleted()
    {
        return !!($this->state & self::STATE_DELETED);
    }

    /**
     * Return whether the object has just been undeleted
     *
     * @return boolean Object has just been undeleted
     */
    public function hasBeenUndeleted()
    {
        return !!($this->state & self::STATE_UNDELETED);
    }

    /**
     * Return whether the object is in modified state
     *
     * @return boolean Modified state
     */
    public function hasBeenModified()
    {
        return !!($this->state & self::STATE_MODIFIED);
    }

    /**
     * Set the object state to mutated
     */
    protected function setMutatedState()
    {
        // Make this object a draft if not already the case and not just
        if (!$this->isDraft() && !$this->hasBeenPublished()) {
            // TODO: Send signal
            $this->convertToDraft();
        }

        // Enable the mutated state
        $this->state |= self::STATE_MUTATED;

        // Enable the modified state
        $this->setModifiedState();
    }

    /**
     * Return the object draft mode
     *
     * @return boolean Object draft mode
     */
    public function isDraft()
    {
        return $this->systemProperties->isDraft();
    }

    /**
     * Return whether the object has just been published
     *
     * @return boolean Object has just been published
     */
    public function hasBeenPublished()
    {
        return !!($this->state & self::STATE_PUBLISHED);
    }

    /**
     * Set the object state to modified
     */
    protected function setModifiedState()
    {
        // If this object is not in modified state yet
        if (!($this->state & self::STATE_MODIFIED)) {
            // TODO: Send signal
        }

        // Enable the modified state
        $this->state |= self::STATE_MODIFIED;

        // Update the modification timestamp
        $this->setSystemProperties($this->systemProperties->touch(), true);
    }

    /**
     * Set the system properties collection
     *
     * @param SystemProperties $systemProperties System property collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    abstract protected function setSystemProperties(SystemProperties $systemProperties, $overwrite = false);

    /**
     * Set the object state to deleted
     */
    protected function setDeletedState()
    {
        // If this object is not in deleted state yet
        if (!($this->state & self::STATE_DELETED)) {
            // TODO: Send signal
        }

        // Enable the deleted state
        $this->state |= self::STATE_DELETED;
        $this->state &= ~self::STATE_UNDELETED;

        // Update system properties
        $this->setSystemProperties($this->systemProperties->delete(), true);
        $this->setModifiedState();
    }

    /**
     * Set the object state to undeleted
     */
    protected function setUndeletedState()
    {
        // If this object is in deleted state
        if ($this->state & self::STATE_DELETED) {
            // TODO: Send signal
        }

        // Disable the deleted state
        $this->state |= self::STATE_UNDELETED;
        $this->state &= ~self::STATE_DELETED;

        // Update system properties
        $this->setSystemProperties($this->systemProperties->undelete(), true);
        $this->setModifiedState();
    }

    /**
     * Set the object state to published
     */
    protected function setPublishedState()
    {
        // If this object is not in published state yet
        if (!($this->state & self::STATE_PUBLISHED)) {
            // TODO: Send signal
        }

        // Set the published flag
        $this->state |= self::STATE_PUBLISHED;

        // Update system properties
        $this->setSystemProperties($this->systemProperties->publish(), true);
        $this->setModifiedState();
    }

    /**
     * Reset the object state
     */
    protected function resetState()
    {
        // If this object is not clean
        if ($this->state != self::STATE_CLEAN) {
            // TODO: Send signal
        }

        $this->state = self::STATE_CLEAN;
    }

    /**
     * Convert this object revision into a draft
     */
    abstract protected function convertToDraft();
}
