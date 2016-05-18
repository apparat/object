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

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\GenericPropertiesInterface;
use Apparat\Object\Domain\Model\Properties\ProcessingInstructions;

/**
 * Processing instructions trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 * @property array $collectionStates
 */
trait ProcessingInstructionsTrait
{
    /**
     * Processing instructions
     *
     * @var GenericPropertiesInterface
     */
    protected $processingInstructions;

    /**
     * Get a processing instruction
     *
     * @param string $procInst Processing instruction name
     * @return mixed Processing instruction
     */
    public function getProcessingInstruction($procInst)
    {
        return $this->processingInstructions->getProperty($procInst);
    }

    /**
     * Set a processing instruction
     *
     * @param string $procInst Processing instruction name
     * @param mixed $value Processing instruction
     * @return ObjectInterface Self reference
     */
    public function setProcessingInstruction($procInst, $value)
    {
        $this->setProcessingInstructions($this->processingInstructions->setProperty($procInst, $value));
        return $this;
    }

    /**
     * Set the processing instruction collection
     *
     * @param GenericPropertiesInterface $procInstructions Processing instruction collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    protected function setProcessingInstructions(GenericPropertiesInterface $procInstructions, $overwrite = false)
    {
        $this->processingInstructions = $procInstructions;
        $procInstState = spl_object_hash($this->processingInstructions);

        // If the domain property collection state has changed
        if (!$overwrite
            && !empty($this->collectionStates[ProcessingInstructions::COLLECTION])
            && ($procInstState !== $this->collectionStates[ProcessingInstructions::COLLECTION])
        ) {
            // Flag this object as modified
            $this->setModifiedState();
        }

        $this->collectionStates[ProcessingInstructions::COLLECTION] = $procInstState;
    }

    /**
     * Set the object state to modified
     */
    abstract protected function setModifiedState();
}
