<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser;

use Exception;
use JsonException;
use NginxConfigParser\Directives\Comment;
use NginxConfigParser\Directives\Directive;
use NginxConfigParser\Directives\Scope;
use NginxConfigParser\Helper\DirectiveHelper;
use NginxConfigParser\Helper\ScopeHelper;
use NginxConfigParser\Source\File;
use NginxConfigParser\Source\Text;

/**
 * Class NginxConfig
 * @package NginxConfigParser
 */
class NginxConfig
{
    /**
     * @return Scope
     */
    public static function createScope(): Scope
    {
        return ScopeHelper::create();
    }

    /**
     * @param string $config
     * @return Scope
     * @throws Exception
     */
    public static function createScopeFromString(string $config): Scope
    {
        return ScopeHelper::createFromString(new Text($config));
    }

    /**
     * @param string $fileName
     * @return Scope
     * @throws Exception
     */
    public static function createScopeFromFile(string $fileName): Scope
    {
        return ScopeHelper::createFromString(new File($fileName));
    }

    /**
     * @param string|array $jsonString
     * @return Scope
     * @throws Exception
     * @throws JsonException
     */
    public static function createScopeFromJson($jsonString): Scope
    {
        if (is_string($jsonString)) {
            $json = json_decode($jsonString, true);
            if (json_last_error()) {
                throw new JsonException('Error parse json config: '.json_last_error_msg());
            }
        } elseif (is_array($jsonString) || is_object($jsonString)) {
            $json = $jsonString;
        } else {
            throw new Exception('Invalid format json');
        }
        return ScopeHelper::createFromJson($json);
    }

    /**
     * @param Scope $scope
     * @return array
     */
    public static function generateJsonFromScope(Scope $scope): array
    {
        $response = [];
        foreach ($scope->getDirectives() as $directive) {
            $name = $directive->getName();
            $value = $directive->getValue();
            $childScope = $directive->getChildScope();
            if ($value == '' && $childScope !== null) {
                $response[$name]['scopes'][] = self::generateJsonFromScope($childScope);
            } elseif ($value != '' && $childScope !== null) {
                $response[$name]['values'][] = $value;
                $response[$name]['scopes'][] = self::generateJsonFromScope($childScope);
            } else {
                $response[$name]['values'][] = $value;
            }
        }
        return $response;
    }

    /**
     * @param array $json
     * @param string $sslCertificatePath
     * @param string $sslKeyPath
     * @return Scope
     */
    public static function generateSslScopeFromJson(array $json, string $sslCertificatePath, string $sslKeyPath): Scope
    {
        return ScopeHelper::generateSslScopeFromJson($json, $sslCertificatePath, $sslKeyPath);
    }

    /**
     * @param Scope $scope
     * @param string $sslCertificatePath
     * @param string $sslKeyPath
     * @return Scope
     */
    public static function generateSslScopeFromScope(Scope $scope, string $sslCertificatePath, string $sslKeyPath): Scope
    {
        return self::generateSslScopeFromJson(self::generateJsonFromScope($scope), $sslCertificatePath, $sslKeyPath);
    }
    /**
     * @param Scope $scope
     * @param string $fileName
     * @throws Exception
     */
    public static function saveScopeToFile(Scope $scope, string $fileName)
    {
        $handle = @fopen($fileName, 'w');
        if (false === $handle) {
            throw new Exception('Cannot open file "' . $fileName . '" for writing.');
        }

        $bytesWritten = @fwrite($handle, self::getConfigFromScope($scope));
        if (false === $bytesWritten || 0 === $bytesWritten) {
            fclose($handle);
            throw new Exception('Cannot write into file "' . $fileName . '".');
        }

        $closed = @fclose($handle);
        if (false === $closed) {
            throw new Exception('Cannot close file handle for "' . $fileName . '".');
        }
    }

    /**
     * @param Scope $scope
     * @return string
     */
    public static function getConfigFromScope(Scope $scope):string
    {
        return (string)$scope;
    }

    /**
     * @param string $name
     * @param null $value
     * @param Scope|null $childScope
     * @param Scope|null $parentScope
     * @param Comment|null $comment
     * @return Directive
     */
    public static function createDirective(string $name, $value = null, Scope $childScope = null, Scope $parentScope = null,
                                           Comment $comment = null): Directive
    {
        return new Directive($name, $value, $childScope, $parentScope, $comment);
    }

    /**
     * @param string $configString
     * @return Directive
     * @throws Exception
     */
    public static function createDirectiveFromString(string $configString): Directive
    {
        return DirectiveHelper::createFromString(new Text($configString));
    }

    /**
     * @param array $directive
     * @param string $key
     * @param null $value
     * @param array|null $scope
     * @return array
     */
    public static function replaceJsonValueInDirective(array $directive, string $key, $value = null, ?array $scope = null): array
    {
        return DirectiveHelper::replaceJsonValueInDirective($directive, $key, $value, $scope);
    }

    /**
     * @param array $directive
     * @param string $key
     * @param null $value
     * @param array|null $scope
     * @return array
     */
    public static function addJsonValueToDirective(array $directive, string $key, $value = null, ?array $scope = null): array
    {
        return DirectiveHelper::addJsonValueToDirective($directive, $key, $value, $scope);
    }
}
