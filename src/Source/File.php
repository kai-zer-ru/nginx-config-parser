<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Source;

use Exception;

/**
 * Class File
 * @package NginxConfigParser
 */
class File extends Text
{
    private string $inFilePath;

    /**
     * @param $filePath string Name of the conf file (or full path).
     * @throws Exception
     */
    public function __construct(string $filePath)
    {
        $this->inFilePath = $filePath;

        $contents = @file_get_contents($this->inFilePath);

        if (false === $contents) {
            throw new Exception('Cannot read file "' . $this->inFilePath . '".');
        }

        parent::__construct($contents);
    }
}
