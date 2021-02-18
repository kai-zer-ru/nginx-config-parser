<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Directives;

/**
 * Class Printable
 * @package NginxConfigParser
 */
abstract class Printable
{
    /**
     * @param $indentLevel
     * @param int $spacesPerIndent
     * @return string
     */
    abstract public function prettyPrint($indentLevel, $spacesPerIndent = 4):string;

    /** @return string */
    public function __toString(): string
    {
        return $this->prettyPrint(0);
    }
}
