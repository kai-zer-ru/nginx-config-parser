<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Directives;

/**
 * Class Comment
 * @package NginxConfigParser
 */
class Comment extends Printable
{
    private ?string $text;

    /**
     * Comment constructor.
     * @param null $text
     */
    public function __construct($text = null)
    {
        $this->text = $text;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return ((is_null($this->text)) || ('' === $this->text));
    }

    /**
     * @return bool
     */
    public function isMultiline(): bool
    {
        return (false !== strpos(rtrim($this->text), "\n"));
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text)
    {
        $this->text = $text;
    }

    /**
     * @param $indentLevel
     * @param int $spacesPerIndent
     * @return string
     */
    public function prettyPrint($indentLevel, $spacesPerIndent = 4): string
    {
        if (true === $this->isEmpty()) {
            return '';
        }

        $indent = str_repeat(str_repeat(' ', $spacesPerIndent), $indentLevel);
        $text = $indent . "# " . $this->text;

        if (true === $this->isMultiline()) {
            $text = preg_replace("#\r{0,1}\n#", PHP_EOL . $indent . "# ", $text);
        }

        return $text . "\n";
    }
}
