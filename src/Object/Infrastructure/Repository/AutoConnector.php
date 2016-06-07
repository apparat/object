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

namespace Apparat\Object\Infrastructure\Repository;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Path\Url;
use Apparat\Object\Domain\Repository\AutoConnectorInterface;

/**
 * Repository auto-connector service
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 */
class AutoConnector implements AutoConnectorInterface
{
    /**
     * Auto-connect a repository with URL default settings
     *
     * @param string $url Repository URL
     * @return boolean Success
     */
    public function connect($url)
    {
        // If it's an absolute URL
        /** @var Url $url */
        $url = Kernel::create(Url::class, [$url]);
        $config = $url->isAbsolute() ? $this->getAbsoluteUrlConfig() : $this->getRelativeUrlConfig($url);

        // If a repository configuration has been created
        if ($config !== null) {
            $repository = Repository::register(strval($url), $config);
            return $repository instanceof \Apparat\Object\Domain\Repository\Repository;
        }

        return true;
    }

    /**
     * Get the repository configuration for an absolute URL
     *
     * @return null Repository configuration for an absolute URL
     * @todo
     */
    protected function getAbsoluteUrlConfig()
    {
        return null;
    }

    /**
     * Get the repository configuration for a relative / local URL
     *
     * @param Url $url URL
     * @return array|null Repository configuration for an relative / local URL
     */
    protected function getRelativeUrlConfig(Url $url)
    {
        // Determine the document root (depending on the SAPI)
        $documentRoot = (php_sapi_name() == 'cli') ?
            realpath(getenv('APPARAT_DOCUMENT_ROOT') ?: (ini_get('doc_root') ?: getcwd())) :
            realpath(empty($_SERVER['DOCUMENT_ROOT']) ?
                (getenv('APPARAT_DOCUMENT_ROOT') ?: (ini_get('doc_root') ?: getcwd())) : $_SERVER['DOCUMENT_ROOT']);

        // If the is a document root: Create a file repository configuration
        return strlen($documentRoot) ? [
            'type' => FileAdapterStrategy::TYPE,
            'root' => rtrim($documentRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$url,
        ] : null;
    }
}
