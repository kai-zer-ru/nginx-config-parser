<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Helper;

use Exception;
use NginxConfigParser\Directives\Directive;
use NginxConfigParser\Source\Text;

class DirectiveHelper
{

    /**
     * @param array $directive
     * @param string $key
     * @param mixed $value
     * @param array|null $scope
     * @return string[][]
     */
    public static function addJsonValueToDirective(array $directive, string $key, $value = null, ?array $scope = null): array
    {
        if ($value) {
            $directive[$key]['values'][] = $value;
        }
        if ($scope) {
            $directive[$key]['scopes'][] = $scope;
        }
        return $directive;
    }

    /**
     * @param array $directive
     * @param string $key
     * @param mixed $value
     * @param array|null $scope
     * @return string[][]
     */
    public static function replaceJsonValueInDirective(array $directive, string $key, $value = null, ?array $scope = null): array
    {
        if ($value) {
            $directive[$key]['values'] = [$value];
        }
        if ($scope) {
            $directive[$key]['scopes'] = [$scope];
        }
        return $directive;
    }

    /**
     * @param Text $configString
     * @return Directive
     * @throws Exception
     */
    public static function createFromString(Text $configString): Directive
    {
        $text = '';
        while (false === $configString->isEndOfFile()) {
            $char = $configString->getCharAtPosition();
            if ('{' === $char) {
                return self::newDirectiveWithScope($text, $configString);
            }
            if (';' === $char) {
                return self::newDirectiveWithoutScope($text, $configString);
            }
            $text .= $char;
            $configString->incrementPosition();
        }
        throw new Exception('Could not create directive.');
    }

    /**
     * @param string $nameString
     * @param Text $scopeString
     * @return Directive
     * @throws Exception
     */
    private static function newDirectiveWithScope(string $nameString, Text $scopeString): Directive
    {
        $scopeString->incrementPosition();
        list($name, $value) = self::processText($nameString);
        $directive = new Directive($name, $value);

        $comment = CommentHelper::checkRestOfTheLineForComment($scopeString);
        if (false !== $comment) {
            $directive->setComment($comment);
        }

        $childScope = ScopeHelper::createFromString($scopeString);
        $childScope->setParentDirective($directive);
        $directive->setChildScope($childScope);

        $scopeString->incrementPosition();

        $comment = CommentHelper::checkRestOfTheLineForComment($scopeString);
        if (false !== $comment) {
            $directive->setComment($comment);
        }

        return $directive;
    }

    /**
     * @param string $nameString
     * @param Text $configString
     * @return Directive
     * @throws Exception
     */
    private static function newDirectiveWithoutScope(string $nameString, Text $configString): Directive
    {
        $configString->incrementPosition();
        list($name, $value) = self::processText($nameString);
        $directive = new Directive($name, $value);

        $comment = CommentHelper::checkRestOfTheLineForComment($configString);
        if (false !== $comment) {
            $directive->setComment($comment);
        }

        return $directive;
    }

    /**
     * @param string $text
     * @return array|false
     * @throws Exception
     */
    private static function processText(string $text)
    {
        $result = self::checkKeyValue($text);
        if (is_array($result)) {
            return $result;
        }
        $result = self::checkKey($text);
        if (is_array($result)) {
            return $result;
        }
        throw new Exception('Text "' . $text . '" did not match pattern.');
    }

    /**
     * @param $text
     * @return array|false
     */
    private static function checkKeyValue($text)
    {
        if (1 === preg_match('#^([a-z][a-z0-9._/+-]*)\s+([^;{]+)$#', $text, $matches)) {
            return array($matches[1], rtrim($matches[2]));
        }
        return false;
    }

    /**
     * @param $text
     * @return array|false
     */
    private static function checkKey($text)
    {
        if (1 === preg_match('#^([a-z][a-z0-9._/+-]*)\s*$#', $text, $matches)) {
            return array($matches[1], null);
        }
        return false;
    }
}
