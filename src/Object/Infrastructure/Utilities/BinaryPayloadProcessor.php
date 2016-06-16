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

use Apparat\Object\Application\Contract\BinaryPayloadProcessorInterface;
use Apparat\Object\Application\Model\Object\AbstractBinaryObject;
use Apparat\Object\Ports\Exceptions\InvalidArgumentException;
use Apparat\Object\Ports\Exceptions\RuntimeException;

/**
 * Binary payload processor
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 */
class BinaryPayloadProcessor extends AbstractPayloadProcessor implements BinaryPayloadProcessorInterface
{
    /**
     * Owning binary object
     *
     * @var AbstractBinaryObject
     */
    protected $object;
    /**
     * Persistence queue
     *
     * @var array
     */
    protected $persistQueue = [];

    /**
     * Process the payload of an object
     *
     * @param string $payload Payload
     * @return string Processed payload
     * @throws InvalidArgumentException If the payload is not a valid file
     * @throws RuntimeException If the payload resource cannot be imported
     */
    public function processPayload($payload)
    {
        // If the payload is not a valid file
        if (!strlen($payload) || !is_file($payload)) {
            throw new InvalidArgumentException(
                sprintf('Invalid binary payload source "%s"', $payload),
                InvalidArgumentException::INVALID_BINARY_PAYLOAD_SOURCE
            );
        }

        $adapterStrategy = $this->object->getRepositoryLocator()->getRepository()->getAdapterStrategy();
        $currentPayload = $this->object->getPayload();
        $currentPayloadHash = strlen($currentPayload) ? $adapterStrategy->getResourceHash($currentPayload) : null;
        $payloadHash = File::hash($payload);

        // If there is no payload yet or if it's different from the current one
        if (($currentPayloadHash === null) || ($currentPayloadHash !== $payloadHash)) {
            $payloadFileExt = pathinfo($payload, PATHINFO_EXTENSION);
            $currentPayload = $this->object->getId()->getId().'.'.$payloadHash;
            $currentPayload .= strlen($payloadFileExt) ? '.'.$payloadFileExt : '';

            // Register the resource in the persistence queue
            $this->persistQueue[$payload] = $currentPayload;
        }

        return $currentPayload;
    }

    /**
     * Post persistence callback
     *
     * @return void
     */
    public function persist()
    {
        // If there are entries in the persistence queue
        if (count($this->persistQueue)) {
            $adapterStrategy = $this->object->getRepositoryLocator()->getRepository()->getAdapterStrategy();
            $containerPath = dirname(strval($this->object->getRepositoryLocator())).DIRECTORY_SEPARATOR;

            // Run through all resources in the persistence queue
            foreach ($this->persistQueue as $source => $target) {
                // If the payload resource cannot be imported
                if (!$adapterStrategy->importResource($source, $containerPath.$target)) {
                    throw new RuntimeException(
                        sprintf('Cannot import binary resource "%s"', $source),
                        RuntimeException::CANNOT_IMPORT_BINARY_RESOURCE
                    );
                }
            }

            // Reset the persistence queue
            $this->persistQueue = [];
        }
    }
}
