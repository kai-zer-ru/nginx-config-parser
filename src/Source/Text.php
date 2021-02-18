<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Source;

use Exception;

/**
 * Class Text
 * @package NginxConfigParser
 */
class Text
{
    private string $data;

    private int $position;

    /**
     * Text constructor.
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->position = 0;
        $this->data = $data;
    }

    /**
     * @param int|null $position
     * @return string
     * @throws Exception
     */
    public function getCharAtPosition(int $position = null): string
    {
        if (is_null($position)) {
            $position = $this->position;
        }

        if ($this->isEndOfFile()) {
            throw new Exception('Index out of range. Position: ' . $position . '.');
        }

        return $this->data[$position];
    }

    /**
     * @param int|null $position
     * @return string
     * @throws Exception
     */
    public function getRestOfTheLine(?int $position = null): string
    {
        if (is_null($position)) {
            $position = $this->position;
        }
        $text = '';
        while ((false === $this->isEndOfFile($position)) && (false === $this->isEndOfLine($position))) {
            $text .= $this->getCharAtPosition($position);
            $position++;
        }
        return $text;
    }

    /**
     * @param int|null $position
     * @return bool
     * @throws Exception
     */
    public function isEndOfLine(?int $position = null): bool
    {
        return (("\r" === $this->getCharAtPosition($position)) || ("\n" === $this->getCharAtPosition($position)));
    }

    /**
     * @param int|null $position
     * @return bool
     */
    public function isEmptyLine(?int $position = null): bool
    {
        $line = $this->getCurrentLine($position);
        return (0 === strlen(trim($line)));
    }

    /**
     * @param int|null $position
     * @return string
     */
    public function getCurrentLine(?int $position = null): string
    {
        if (is_null($position)) {
            $position = $this->position;
        }

        $offset = $this->getLastEndOfLine($position);
        $length = $this->getNextEndOfLine($position) - $offset;
        return substr($this->data, $offset, $length);
    }

    /**
     * @param int|null $position
     * @return int
     */
    public function getLastEndOfLine(?int $position = null): int
    {
        if (is_null($position)) {
            $position = $this->position;
        }

        return strrpos(substr($this->data, 0, $position), "\n");
    }

    /**
     * @param int|null $position
     * @return int
     */
    public function getNextEndOfLine(?int $position = null): int
    {
        if (is_null($position)) {
            $position = $this->position;
        }

        $eolPosition = strpos($this->data, "\n", $position);
        if (false === $eolPosition) {
            $eolPosition = strlen($this->data) - 1;
        }

        return $eolPosition;
    }

    /**
     * @param int|null $position
     * @return bool
     */
    public function isEndOfFile(?int $position = null): bool
    {
        if (is_null($position)) {
            $position = $this->position;
        }
        return (!isset($this->data[$position]));
    }

    /**
     * @param int $inc
     */
    public function incrementPosition($inc = 1)
    {
        $this->position += $inc;
    }

    /**
     * @param int|null $position
     */
    public function goToNextEol(?int $position = null)
    {
        if (is_null($position)) {
            $position = $this->position;
        }
        $this->position = $this->getNextEndOfLine($position);
    }
}
