<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Helper;

use Exception;
use NginxConfigParser\Directives\Directive;
use NginxConfigParser\Directives\Scope;
use NginxConfigParser\Source\Text;

class ScopeHelper
{
    public static function create(): Scope
    {
        return new Scope();
    }

    /**
     * @param Text $configString
     * @return Scope
     * @throws Exception
     */
    public static function createFromString(Text $configString): Scope
    {
        $scope = self::create();
        while (false === $configString->isEndOfFile()) {
            if (true === $configString->isEmptyLine()) {
                $scope->addPrintable(EmptyLineHelper::createEmptyLineFromText($configString));
            }
            $char = $configString->getCharAtPosition();
            if ('#' === $char) {
                $scope->addPrintable(CommentHelper::createCommentFromText($configString));
                continue;
            }
            if (('a' <= $char) && ('z' >= $char)) {
                $scope->addDirective(DirectiveHelper::createFromString($configString));
                continue;
            }
            if ('}' === $configString->getCharAtPosition()) {
                break;
            }
            $configString->incrementPosition();
        }
        return $scope;
    }

    /**
     * @param array $json
     * @return Scope
     */
    public static function createFromJson(array $json): Scope
    {
        $mainScope = self::create();
        foreach ($json as $directiveName => $directiveValue) {
            $values = $directiveValue['values'] ?? null;
            $scopes = $directiveValue['scopes'] ?? null;
            $scopesAdded = false;
            if ($values) {
                foreach ($values as $value) {
                    $childDirective = new Directive($directiveName);
                    $childDirective->setValue($value);
                    if ($scopes) {
                        foreach ($scopes as $scope) {
                            $childScope = self::createFromJson($scope);
                            $childDirective->setChildScope($childScope);
                        }
                        $scopesAdded = true;
                    }
                    $mainScope->addDirective($childDirective);
                }
            }
            if ($scopes && !$scopesAdded) {
                foreach ($scopes as $scope) {
                    $childDirective = new Directive($directiveName);
                    $childScope = self::createFromJson($scope);
                    $childDirective->setChildScope($childScope);
                    $mainScope->addDirective($childDirective);
                }
            }
        }
        return $mainScope;
    }

    /**
     * @param array $json
     * @param string $sslCertificatePath
     * @param string $sslKeyPath
     * @return Scope
     */
    public static function generateSslScopeFromJson(array $json, string $sslCertificatePath, string $sslKeyPath): Scope
    {
        $resultScopes = [];
        $jsonScopes = $json['server']['scopes'];
        $scope80 = $jsonScopes[0];
        $scope443 = $jsonScopes[0];
        $newScope80 = [];
        foreach ($scope80 as $key => $value) {
            if ($key == 'listen' || $key == 'server_name') {
                $newScope80[$key] = $value;
            }
        }

        $newScope80 = DirectiveHelper::addJsonValueToDirective($newScope80, 'return', '301 https://$host$request_uri');
        $resultScopes[] = $newScope80;

        $scope443 = DirectiveHelper::replaceJsonValueInDirective($scope443, 'listen', '443 ssl http2');
        $scope443 = DirectiveHelper::addJsonValueToDirective($scope443, 'ssl_certificate', $sslCertificatePath);
        $scope443 = DirectiveHelper::addJsonValueToDirective($scope443, 'ssl_certificate_key', $sslKeyPath);
        $resultScopes[] = $scope443;
        $json['server']['scopes'] = $resultScopes;
        return self::createFromJson($json);
    }
}
