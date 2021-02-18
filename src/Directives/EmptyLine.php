<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Directives;

/**
 * Class EmptyLine
 * @package NginxConfigParser
 */
class EmptyLine extends Printable
{
    /**
     * @param $indentLevel
     * @param int $spacesPerIndent
     * @return string
     */
    public function prettyPrint($indentLevel, $spacesPerIndent = 4): string
    {
        return "\n";
    }
}
