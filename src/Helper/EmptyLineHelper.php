<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Helper;

use NginxConfigParser\Directives\EmptyLine;
use NginxConfigParser\Source\Text;

class EmptyLineHelper
{
    /**
     * @param Text $configString
     * @return EmptyLine
     */
    public static function createEmptyLineFromText(Text $configString): EmptyLine
    {
        $configString->gotoNextEol();
        return new EmptyLine();
    }
    /**
     * @param string $configString
     * @return EmptyLine
     */
    public static function createEmptyLineFromString(string $configString): EmptyLine
    {
        return self::createEmptyLineFromText(new Text($configString));
    }
}
