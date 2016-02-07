<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure
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

namespace Apparat\Object\Infrastructure\Repository;


use Apparat\Object\Domain\Model\Path\Url;
use Apparat\Object\Domain\Repository\AutoConnectorInterface;
use Apparat\Object\Ports\Repository;

/**
 * Repository auto-connector service
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 */
class AutoConnector implements AutoConnectorInterface
{

    /**
     * Auto-connect a repository with by URL default settings
     *
     * @param string $url Repository URL
     * @return boolean Success
     */
    public function connect($url)
    {
        echo "Auto-connecting: $url\n";
        $config = null;

        // If it's an absolute URL
        $url = new Url($url);
        if ($url->isAbsolute()) {


            // Else: Relative / local URL -> Instantiate as file repository
        } else {

            // If this is run via CLI
            if (PHP_SAPI == 'cli') {
                $documentRoot = ini_get('doc_root') ?: getcwd();

                // Else: Use the server's document root
            } else {
                $documentRoot = empty($_SERVER['DOCUMENT_ROOT']) ? ini_get('doc_root') : $_SERVER['DOCUMENT_ROOT'];
            }

            // If the is a document root: Create a file repository configuration
            if (strlen($documentRoot)) {
                $config = [
                    'type' => FileAdapterStrategy::TYPE,
                    'root' => rtrim($documentRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$url,
                ];
            }
        }

        // If a repository configuration has been created
        if ($config !== null) {
            return Repository::register($url, $config) instanceof \Apparat\Object\Domain\Repository\Repository;
        }

        return true;
    }
}
