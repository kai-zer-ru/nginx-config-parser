<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Helper;

use Exception;
use NginxConfigParser\Directives\Comment;
use NginxConfigParser\Source\Text;

class CommentHelper
{
    /**
     * @param Text $configString
     * @return Comment
     * @throws Exception
     */
    public static function createCommentFromText(Text $configString): Comment
    {
        $text = '';
        while ((false === $configString->isEndOfFile()) && (false === $configString->isEndOfLine())) {
            $text .= $configString->getCharAtPosition();
            $configString->incrementPosition();
        }
        return new Comment(ltrim($text, "# "));
    }
    /**
     * @param Text $configString
     * @return Comment
     * @throws Exception
     */
    public static function createCommentFromString(string $configString): Comment
    {
        return self::createCommentFromText(new Text($configString));
    }
    /**
     * @param Text $configString
     * @return false|Comment
     * @throws Exception
     */
    public static function checkRestOfTheLineForComment(Text $configString)
    {
        $restOfTheLine = $configString->getRestOfTheLine();
        if (1 !== preg_match('/^\s*#/', $restOfTheLine)) {
            return false;
        }

        $commentPosition = strpos($restOfTheLine, '#');
        $configString->incrementPosition($commentPosition);
        return self::createCommentFromText($configString);
    }
}
