<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser;

use Exception;
use NginxConfigParser\Source\Text;
use PHPUnit\Framework\TestCase;

/**
 * Class TextTest
 * @package NginxConfigParser
 */
class TextTest extends TestCase
{
    /**
     * @throws Exception
     * @covers \NginxConfigParser\Source\Text::__construct
     * @covers \NginxConfigParser\Source\Text::isEndOfFile
     * @covers \NginxConfigParser\Source\Text::getCharAtPosition
     */
    public function testGetCharPosition()
    {
        $this->expectException(Exception::class);
        $text = new Text('');
        $text->getCharAtPosition(1.5); // in function position = 1
    }

    /**
     * @throws Exception
     * @covers \NginxConfigParser\Source\Text::getCharAtPosition
     * @covers \NginxConfigParser\Source\Text::__construct
     * @covers \NginxConfigParser\Source\Text::isEndOfFile
     */
    public function testGetCharEof()
    {
        $this->expectException(Exception::class);
        $text = new Text('');
        $text->getCharAtPosition(1);
    }

    /**
     * @covers \NginxConfigParser\Source\Text::getLastEndOfLine
     * @covers \NginxConfigParser\Source\Text::__construct
     */
    public function testGetLastEol()
    {
        $text = new Text('');
        $this->assertEquals(0, $text->getLastEndOfLine());
    }

    /**
     * @covers \NginxConfigParser\Source\Text::getNextEndOfLine
     * @covers \NginxConfigParser\Source\Text::__construct
     * @covers \NginxConfigParser\Source\Text::isEndOfFile
     */
    public function testGetNextEol()
    {
        $text = new Text("\n");
        $this->assertEquals(0, $text->getNextEndOfLine());
        $text = new Text("roman");
        $this->assertEquals(4, $text->getNextEndOfLine());
    }
}
