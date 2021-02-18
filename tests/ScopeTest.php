<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser;

use Exception;
use JsonException;
use NginxConfigParser\Directives\Scope;
use NginxConfigParser\Helper\CommentHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class ScopeTest
 * @package NginxConfigParser
 */
class ScopeTest extends TestCase
{
    /**
     * @throws Exception
     * @covers \NginxConfigParser\Helper\CommentHelper::createCommentFromText
     * @covers \NginxConfigParser\Helper\EmptyLineHelper::createEmptyLineFromText
     * @covers \NginxConfigParser\Directives\EmptyLine::prettyPrint
     * @covers \NginxConfigParser\Source\Text::goToNextEol
     * @covers \NginxConfigParser\NginxConfig::createScopeFromFile
     * @covers \NginxConfigParser\NginxConfig::saveScopeToFile
     * @covers \NginxConfigParser\Helper\CommentHelper::checkRestOfTheLineForComment
     * @covers \NginxConfigParser\Helper\CommentHelper::createCommentFromString
     * @covers \NginxConfigParser\Helper\DirectiveHelper::checkKey
     * @covers \NginxConfigParser\Helper\DirectiveHelper::checkKeyValue
     * @covers \NginxConfigParser\Helper\DirectiveHelper::createFromString
     * @covers \NginxConfigParser\Helper\DirectiveHelper::newDirectiveWithScope
     * @covers \NginxConfigParser\Helper\DirectiveHelper::newDirectiveWithoutScope
     * @covers \NginxConfigParser\Helper\DirectiveHelper::processText
     * @covers \NginxConfigParser\Helper\ScopeHelper::create
     * @covers \NginxConfigParser\Helper\ScopeHelper::createFromString
     * @covers \NginxConfigParser\NginxConfig::getConfigFromScope
     * @covers \NginxConfigParser\Directives\Comment::__construct
     * @covers \NginxConfigParser\Directives\Comment::isEmpty
     * @covers \NginxConfigParser\Directives\Comment::isMultiline
     * @covers \NginxConfigParser\Directives\Comment::prettyPrint
     * @covers \NginxConfigParser\Directives\Directive::__construct
     * @covers \NginxConfigParser\Directives\Directive::getChildScope
     * @covers \NginxConfigParser\Directives\Directive::getComment
     * @covers \NginxConfigParser\Directives\Directive::getParentScope
     * @covers \NginxConfigParser\Directives\Directive::hasComment
     * @covers \NginxConfigParser\Directives\Directive::prettyPrint
     * @covers \NginxConfigParser\Directives\Directive::setChildScope
     * @covers \NginxConfigParser\Directives\Directive::setComment
     * @covers \NginxConfigParser\Directives\Directive::setParentScope
     * @covers \NginxConfigParser\Directives\Scope::__toString
     * @covers \NginxConfigParser\Directives\Scope::addDirective
     * @covers \NginxConfigParser\Directives\Scope::addPrintable
     * @covers \NginxConfigParser\Directives\Scope::getParentDirective
     * @covers \NginxConfigParser\Directives\Scope::prettyPrint
     * @covers \NginxConfigParser\Directives\Scope::setParentDirective
     * @covers \NginxConfigParser\Source\File::__construct
     * @covers \NginxConfigParser\Source\Text::__construct
     * @covers \NginxConfigParser\Source\Text::getCharAtPosition
     * @covers \NginxConfigParser\Source\Text::getCurrentLine
     * @covers \NginxConfigParser\Source\Text::getLastEndOfLine
     * @covers \NginxConfigParser\Source\Text::getNextEndOfLine
     * @covers \NginxConfigParser\Source\Text::getRestOfTheLine
     * @covers \NginxConfigParser\Source\Text::incrementPosition
     * @covers \NginxConfigParser\Source\Text::isEmptyLine
     * @covers \NginxConfigParser\Source\Text::isEndOfFile
     * @covers \NginxConfigParser\Source\Text::isEndOfLine
     */
    public function testFromFile()
    {
        $this->prepareBuild();
        $scope = NginxConfig::createScopeFromFile(__DIR__ . '/config/test_input.conf');
        NginxConfig::saveScopeToFile($scope, __DIR__.'/build/out.conf');
        $this->assertEquals(@file_get_contents(__DIR__ . '/config/test_input.conf'), @file_get_contents(__DIR__.'/build/out.conf'));
        $this->removeBuild();
    }

    /**
     * @throws Exception
     * @covers \NginxConfigParser\NginxConfig::saveScopeToFile
     */
    public function testSaveToFile()
    {
        $this->expectException(Exception::class);
        $scope = new Scope();
        NginxConfig::saveScopeToFile($scope, 'this/path/does/not/exist.conf');
    }

    /**
     * @throws Exception
     * @covers \NginxConfigParser\NginxConfig::getConfigFromScope
     * @covers \NginxConfigParser\Directives\Scope::__toString
     * @covers \NginxConfigParser\Directives\Scope::prettyPrint
     * @covers \NginxConfigParser\NginxConfig::saveScopeToFile
     */
    public function testSaveToUnreadableFile()
    {
        $this->expectException(Exception::class);
        $this->prepareBuild();
        file_put_contents(__DIR__.'/build/unreadable.conf', '');
        chmod(__DIR__.'/build/unreadable.conf', 444);
        $scope = new Scope();
        NginxConfig::saveScopeToFile($scope, __DIR__.'/build/unreadable.conf');
        $this->removeBuild();
    }

    /**
     * @covers \NginxConfigParser\Helper\CommentHelper::createCommentFromString
     * @covers \NginxConfigParser\Helper\CommentHelper::createCommentFromText
     * @covers \NginxConfigParser\Directives\Directive::setComment
     * @covers \NginxConfigParser\Source\Text::__construct
     * @covers \NginxConfigParser\Source\Text::getCharAtPosition
     * @covers \NginxConfigParser\Source\Text::incrementPosition
     * @covers \NginxConfigParser\Source\Text::isEndOfFile
     * @covers \NginxConfigParser\Source\Text::isEndOfLine
     * @covers \NginxConfigParser\NginxConfig::createDirective
     * @covers \NginxConfigParser\Directives\Directive::setCommentText
     * @covers \NginxConfigParser\Directives\Directive::__toString
     * @covers \NginxConfigParser\Helper\ScopeHelper::create
     * @covers \NginxConfigParser\NginxConfig::createScope
     * @covers \NginxConfigParser\Directives\Comment::__construct
     * @covers \NginxConfigParser\Directives\Comment::isEmpty
     * @covers \NginxConfigParser\Directives\Comment::isMultiline
     * @covers \NginxConfigParser\Directives\Comment::prettyPrint
     * @covers \NginxConfigParser\Directives\Comment::setText
     * @covers \NginxConfigParser\Directives\Directive::__construct
     * @covers \NginxConfigParser\Directives\Directive::getChildScope
     * @covers \NginxConfigParser\Directives\Directive::getComment
     * @covers \NginxConfigParser\Directives\Directive::getParentScope
     * @covers \NginxConfigParser\Directives\Directive::hasComment
     * @covers \NginxConfigParser\Directives\Directive::prettyPrint
     * @covers \NginxConfigParser\Directives\Directive::setChildScope
     * @covers \NginxConfigParser\Directives\Directive::setParentScope
     * @covers \NginxConfigParser\Directives\Scope::__toString
     * @covers \NginxConfigParser\Directives\Scope::addDirective
     * @covers \NginxConfigParser\Directives\Scope::addPrintable
     * @covers \NginxConfigParser\Directives\Scope::getParentDirective
     * @covers \NginxConfigParser\Directives\Scope::prettyPrint
     * @covers \NginxConfigParser\Directives\Scope::setParentDirective
     */
    public function testCreate()
    {
        $configString = (string) NginxConfig::createScope()
            ->addDirective(
                NginxConfig::createDirective('server')
                ->setChildScope(
                    NginxConfig::createScope()
                    ->addDirective(NginxConfig::createDirective('listen', 8080))
                    ->addDirective(NginxConfig::createDirective('server_name', 'example.net'))
                    ->addDirective(NginxConfig::createDirective('root', 'C:/www/example_net'))
                    ->addDirective(
                        NginxConfig::createDirective(
                            'location',
                            '^~ /var/',
                            NginxConfig::createScope()->addDirective(NginxConfig::createDirective('deny', 'all')
                                ->setComment(CommentHelper::createCommentFromString('deny'))),
                            null,
                            CommentHelper::createCommentFromString('Deny access for location /var/')
                        )
                    )
                )
            )->__toString();
        $this->assertEquals($configString, @file_get_contents(__DIR__ . '/config/scope_create_output.conf'));
    }

    /**
     * @throws Exception
     * @covers \NginxConfigParser\Helper\ScopeHelper::create
     * @covers \NginxConfigParser\Helper\ScopeHelper::createFromJson
     * @covers \NginxConfigParser\Directives\Directive::__construct
     * @covers \NginxConfigParser\Directives\Directive::getChildScope
     * @covers \NginxConfigParser\Directives\Directive::getName
     * @covers \NginxConfigParser\Directives\Directive::getParentScope
     * @covers \NginxConfigParser\Directives\Directive::getValue
     * @covers \NginxConfigParser\Directives\Directive::setParentScope
     * @covers \NginxConfigParser\Directives\Directive::setValue
     * @covers \NginxConfigParser\Directives\Scope::addPrintable
     * @covers \NginxConfigParser\Directives\Scope::getDirectives
     * @covers \NginxConfigParser\Directives\Scope::getParentDirective
     * @covers \NginxConfigParser\Directives\Scope::setParentDirective
     * @covers \NginxConfigParser\NginxConfig::createScope
     * @covers \NginxConfigParser\NginxConfig::createDirective
     * @covers \NginxConfigParser\NginxConfig::generateJsonFromScope
     * @covers \NginxConfigParser\NginxConfig::createScopeFromJson
     * @covers \NginxConfigParser\Directives\Directive::setChildScope
     * @covers \NginxConfigParser\Directives\Scope::addDirective
     */
    public function testJson()
    {
        $scope = NginxConfig::createScope()
            ->addDirective(
                NginxConfig::createDirective('server')
                ->setChildScope(
                    NginxConfig::createScope()
                    ->addDirective(NginxConfig::createDirective('listen', 8080))
                    ->addDirective(NginxConfig::createDirective('server_name', 'example.net'))
                    ->addDirective(NginxConfig::createDirective('root', 'C:/www/example_net'))
                    ->addDirective(
                        NginxConfig::createDirective(
                            'location',
                            '^~ /var/',
                            NginxConfig::createScope()
                            ->addDirective(NginxConfig::createDirective('deny', 'all'))
                        )
                    )
                )
            )
            ->addDirective(
                NginxConfig::createDirective('server')
                ->setChildScope(
                    NginxConfig::createScope()
                    ->addDirective(NginxConfig::createDirective('listen', 80))
                    ->addDirective(NginxConfig::createDirective('server_name', 'example.ru'))
                    ->addDirective(NginxConfig::createDirective('root', 'C:/www/example_ru'))
                    ->addDirective(
                        NginxConfig::createDirective(
                            'location',
                            '^~ /var/www',
                            NginxConfig::createScope()
                            ->addDirective(NginxConfig::createDirective('allow', 'all'))
                        )
                    )
                )
            );
        $json = NginxConfig::generateJsonFromScope($scope);
        $scope2 = NginxConfig::createScopeFromJson($json);
        $json2 = NginxConfig::generateJsonFromScope($scope2);
        $this->assertEquals($json, $json2);
    }

    /**
     * @covers \NginxConfigParser\NginxConfig::createScopeFromJson
     */
    public function testScopeFromInvalidJson()
    {
        $this->expectException(JsonException::class);
        $json = '{"server":{"scopes":[{"listen":{"values":["80"]},"server_name":{"values":["my-domain.ru"]},"';
        NginxConfig::createScopeFromJson($json);
    }

    /**
     * @covers \NginxConfigParser\Helper\CommentHelper::checkRestOfTheLineForComment
     * @covers \NginxConfigParser\Helper\DirectiveHelper::checkKey
     * @covers \NginxConfigParser\Helper\DirectiveHelper::checkKeyValue
     * @covers \NginxConfigParser\Helper\DirectiveHelper::createFromString
     * @covers \NginxConfigParser\Helper\DirectiveHelper::newDirectiveWithScope
     * @covers \NginxConfigParser\Helper\DirectiveHelper::newDirectiveWithoutScope
     * @covers \NginxConfigParser\Helper\DirectiveHelper::processText
     * @covers \NginxConfigParser\Helper\ScopeHelper::create
     * @covers \NginxConfigParser\Helper\ScopeHelper::createFromString
     * @covers \NginxConfigParser\NginxConfig::createScopeFromString
     * @covers \NginxConfigParser\NginxConfig::getConfigFromScope
     * @covers \NginxConfigParser\Directives\Comment::__construct
     * @covers \NginxConfigParser\Directives\Comment::isEmpty
     * @covers \NginxConfigParser\Directives\Directive::__construct
     * @covers \NginxConfigParser\Directives\Directive::getChildScope
     * @covers \NginxConfigParser\Directives\Directive::getComment
     * @covers \NginxConfigParser\Directives\Directive::getParentScope
     * @covers \NginxConfigParser\Directives\Directive::hasComment
     * @covers \NginxConfigParser\Directives\Directive::prettyPrint
     * @covers \NginxConfigParser\Directives\Directive::setChildScope
     * @covers \NginxConfigParser\Directives\Directive::setParentScope
     * @covers \NginxConfigParser\Directives\Scope::__toString
     * @covers \NginxConfigParser\Directives\Scope::addDirective
     * @covers \NginxConfigParser\Directives\Scope::addPrintable
     * @covers \NginxConfigParser\Directives\Scope::getParentDirective
     * @covers \NginxConfigParser\Directives\Scope::prettyPrint
     * @covers \NginxConfigParser\Directives\Scope::setParentDirective
     * @covers \NginxConfigParser\Source\Text::__construct
     * @covers \NginxConfigParser\Source\Text::getCharAtPosition
     * @covers \NginxConfigParser\Source\Text::getCurrentLine
     * @covers \NginxConfigParser\Source\Text::getLastEndOfLine
     * @covers \NginxConfigParser\Source\Text::getNextEndOfLine
     * @covers \NginxConfigParser\Source\Text::getRestOfTheLine
     * @covers \NginxConfigParser\Source\Text::incrementPosition
     * @covers \NginxConfigParser\Source\Text::isEmptyLine
     * @covers \NginxConfigParser\Source\Text::isEndOfFile
     * @covers \NginxConfigParser\Source\Text::isEndOfLine
     * @covers \NginxConfigParser\NginxConfig::createScopeFromJson
     */
    public function testCreateScopeFromString()
    {
        $config = file_get_contents(__DIR__ . '/config/wordpress.conf');
        $scope = NginxConfig::createScopeFromString($config);
        $this->assertEquals(NginxConfig::getConfigFromScope($scope), $config);
    }

    /**
     * @covers \NginxConfigParser\NginxConfig::createScopeFromJson
     */
    public function testScopeFromJson()
    {
        $this->expectException(Exception::class);
        $json = 1.2;
        NginxConfig::createScopeFromJson($json);
    }

    /**
     * @throws Exception
     * @covers \NginxConfigParser\NginxConfig::createScopeFromFile
     * @covers \NginxConfigParser\NginxConfig::generateJsonFromScope
     * @covers \NginxConfigParser\NginxConfig::createScopeFromJson
     * @covers \NginxConfigParser\NginxConfig::saveScopeToFile
     * @covers \NginxConfigParser\Helper\CommentHelper::checkRestOfTheLineForComment
     * @covers \NginxConfigParser\Helper\DirectiveHelper::checkKey
     * @covers \NginxConfigParser\Helper\DirectiveHelper::checkKeyValue
     * @covers \NginxConfigParser\Helper\DirectiveHelper::createFromString
     * @covers \NginxConfigParser\Helper\DirectiveHelper::newDirectiveWithScope
     * @covers \NginxConfigParser\Helper\DirectiveHelper::newDirectiveWithoutScope
     * @covers \NginxConfigParser\Helper\DirectiveHelper::processText
     * @covers \NginxConfigParser\Helper\ScopeHelper::create
     * @covers \NginxConfigParser\Helper\ScopeHelper::createFromJson
     * @covers \NginxConfigParser\Helper\ScopeHelper::createFromString
     * @covers \NginxConfigParser\NginxConfig::getConfigFromScope
     * @covers \NginxConfigParser\Directives\Comment::__construct
     * @covers \NginxConfigParser\Directives\Comment::isEmpty
     * @covers \NginxConfigParser\Directives\Directive::__construct
     * @covers \NginxConfigParser\Directives\Directive::getChildScope
     * @covers \NginxConfigParser\Directives\Directive::getComment
     * @covers \NginxConfigParser\Directives\Directive::getName
     * @covers \NginxConfigParser\Directives\Directive::getParentScope
     * @covers \NginxConfigParser\Directives\Directive::getValue
     * @covers \NginxConfigParser\Directives\Directive::hasComment
     * @covers \NginxConfigParser\Directives\Directive::prettyPrint
     * @covers \NginxConfigParser\Directives\Directive::setChildScope
     * @covers \NginxConfigParser\Directives\Directive::setParentScope
     * @covers \NginxConfigParser\Directives\Directive::setValue
     * @covers \NginxConfigParser\Directives\Scope::__toString
     * @covers \NginxConfigParser\Directives\Scope::addDirective
     * @covers \NginxConfigParser\Directives\Scope::addPrintable
     * @covers \NginxConfigParser\Directives\Scope::getDirectives
     * @covers \NginxConfigParser\Directives\Scope::getParentDirective
     * @covers \NginxConfigParser\Directives\Scope::prettyPrint
     * @covers \NginxConfigParser\Directives\Scope::setParentDirective
     * @covers \NginxConfigParser\Source\File::__construct
     * @covers \NginxConfigParser\Source\Text::__construct
     * @covers \NginxConfigParser\Source\Text::getCharAtPosition
     * @covers \NginxConfigParser\Source\Text::getCurrentLine
     * @covers \NginxConfigParser\Source\Text::getLastEndOfLine
     * @covers \NginxConfigParser\Source\Text::getNextEndOfLine
     * @covers \NginxConfigParser\Source\Text::getRestOfTheLine
     * @covers \NginxConfigParser\Source\Text::incrementPosition
     * @covers \NginxConfigParser\Source\Text::isEmptyLine
     * @covers \NginxConfigParser\Source\Text::isEndOfFile
     * @covers \NginxConfigParser\Source\Text::isEndOfLine
     */
    public function testConfigToJsonToConfig()
    {
        $this->prepareBuild();
        $scope = NginxConfig::createScopeFromFile(__DIR__ . '/config/wordpress.conf');
        $json = NginxConfig::generateJsonFromScope($scope);
        $newScope = NginxConfig::createScopeFromJson($json);
        NginxConfig::saveScopeToFile($newScope, __DIR__.'/build/out.conf');
        $this->assertEquals(@file_get_contents(__DIR__ . '/config/wordpress.conf'), @file_get_contents(__DIR__.'/build/out.conf'));
        $this->removeBuild();
    }

    /**
     * @throws Exception
     * @covers \NginxConfigParser\NginxConfig::createScopeFromFile
     * @covers \NginxConfigParser\NginxConfig::generateJsonFromScope
     * @covers \NginxConfigParser\NginxConfig::generateSslScopeFromJson
     * @covers \NginxConfigParser\NginxConfig::generateSslScopeFromScope
     * @covers \NginxConfigParser\NginxConfig::saveScopeToFile
     * @covers \NginxConfigParser\Helper\CommentHelper::checkRestOfTheLineForComment
     * @covers \NginxConfigParser\Helper\DirectiveHelper::addJsonValueToDirective
     * @covers \NginxConfigParser\Helper\DirectiveHelper::checkKey
     * @covers \NginxConfigParser\Helper\DirectiveHelper::checkKeyValue
     * @covers \NginxConfigParser\Helper\DirectiveHelper::createFromString
     * @covers \NginxConfigParser\Helper\DirectiveHelper::newDirectiveWithScope
     * @covers \NginxConfigParser\Helper\DirectiveHelper::newDirectiveWithoutScope
     * @covers \NginxConfigParser\Helper\DirectiveHelper::processText
     * @covers \NginxConfigParser\Helper\DirectiveHelper::replaceJsonValueInDirective
     * @covers \NginxConfigParser\Helper\ScopeHelper::create
     * @covers \NginxConfigParser\Helper\ScopeHelper::createFromJson
     * @covers \NginxConfigParser\Helper\ScopeHelper::createFromString
     * @covers \NginxConfigParser\Helper\ScopeHelper::generateSslScopeFromJson
     * @covers \NginxConfigParser\NginxConfig::getConfigFromScope
     * @covers \NginxConfigParser\Directives\Comment::__construct
     * @covers \NginxConfigParser\Directives\Comment::isEmpty
     * @covers \NginxConfigParser\Directives\Directive::__construct
     * @covers \NginxConfigParser\Directives\Directive::getChildScope
     * @covers \NginxConfigParser\Directives\Directive::getComment
     * @covers \NginxConfigParser\Directives\Directive::getName
     * @covers \NginxConfigParser\Directives\Directive::getParentScope
     * @covers \NginxConfigParser\Directives\Directive::getValue
     * @covers \NginxConfigParser\Directives\Directive::hasComment
     * @covers \NginxConfigParser\Directives\Directive::prettyPrint
     * @covers \NginxConfigParser\Directives\Directive::setChildScope
     * @covers \NginxConfigParser\Directives\Directive::setParentScope
     * @covers \NginxConfigParser\Directives\Directive::setValue
     * @covers \NginxConfigParser\Directives\Scope::__toString
     * @covers \NginxConfigParser\Directives\Scope::addDirective
     * @covers \NginxConfigParser\Directives\Scope::addPrintable
     * @covers \NginxConfigParser\Directives\Scope::getDirectives
     * @covers \NginxConfigParser\Directives\Scope::getParentDirective
     * @covers \NginxConfigParser\Directives\Scope::prettyPrint
     * @covers \NginxConfigParser\Directives\Scope::setParentDirective
     * @covers \NginxConfigParser\Source\File::__construct
     * @covers \NginxConfigParser\Source\Text::__construct
     * @covers \NginxConfigParser\Source\Text::getCharAtPosition
     * @covers \NginxConfigParser\Source\Text::getCurrentLine
     * @covers \NginxConfigParser\Source\Text::getLastEndOfLine
     * @covers \NginxConfigParser\Source\Text::getNextEndOfLine
     * @covers \NginxConfigParser\Source\Text::getRestOfTheLine
     * @covers \NginxConfigParser\Source\Text::incrementPosition
     * @covers \NginxConfigParser\Source\Text::isEmptyLine
     * @covers \NginxConfigParser\Source\Text::isEndOfFile
     * @covers \NginxConfigParser\Source\Text::isEndOfLine
     */
    public function testGenerateSslConfig()
    {
        $this->prepareBuild();
        $scope = NginxConfig::createScopeFromFile(__DIR__ . '/config/wp.80.conf');
        $json = NginxConfig::generateJsonFromScope($scope);
        $sslScope = NginxConfig::generateSslScopeFromJson($json, '/etc/letsencrypt/live/my-domain.ru/fullchain.pem', '/etc/letsencrypt/live/my-domain.ru/privkey.pem');
        NginxConfig::saveScopeToFile($sslScope, __DIR__.'/build/out.conf');
        $this->assertEquals(@file_get_contents(__DIR__ . '/config/wp.443.conf'), @file_get_contents(__DIR__.'/build/out.conf'));

        $scope = NginxConfig::createScopeFromFile(__DIR__ . '/config/wp.80.conf');
        $sslScope = NginxConfig::generateSslScopeFromScope($scope, '/etc/letsencrypt/live/my-domain.ru/fullchain.pem', '/etc/letsencrypt/live/my-domain.ru/privkey.pem');
        NginxConfig::saveScopeToFile($sslScope, __DIR__.'/build/out2.conf');
        $this->assertEquals(@file_get_contents(__DIR__ . '/config/wp.443.conf'), @file_get_contents(__DIR__.'/build/out2.conf'));
        $this->removeBuild();
    }

    private function prepareBuild()
    {
        if (!is_dir(__DIR__.'/build/')) {
            mkdir(__DIR__.'/build/', 0777, true);
        }
    }

    private function removeBuild()
    {
        $files = scandir(__DIR__.'/build');
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                unlink(__DIR__.'/build/'.$file);
            }
        }
        rmdir(__DIR__.'/build');
    }
}
