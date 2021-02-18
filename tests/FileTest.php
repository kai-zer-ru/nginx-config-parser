<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser;

use Exception;
use NginxConfigParser\Source\File;
use PHPUnit\Framework\TestCase;

/**
 * Class FileTest
 * @package NginxConfigParser
 */
class FileTest extends TestCase
{
    /**
     * Fail on non existing file
     * @covers \NginxConfigParser\Source\File
     */
    public function testCannotRead()
    {
        $this->expectException(Exception::class);
        new File('this_file_does_not_exist.txt');
    }
}
