<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Directives;

/**
 * Class Scope
 * @package NginxConfigParser
 */
class Scope extends Printable
{
    private ?Directive $parentDirective = null;
    private array $directives = [];
    private array $printables = [];

    /**
     * @return Directive|null
     */
    public function getParentDirective(): ?Directive
    {
        return $this->parentDirective;
    }

    /**
     * @param Directive $directive
     * @return $this
     */
    public function addDirective(Directive $directive): self
    {
        if ($directive->getParentScope() !== $this) {
            $directive->setParentScope($this);
        }

        $this->directives[] = $directive;
        $this->addPrintable($directive);

        return $this;
    }

    /**
     * @param Printable $printable
     */
    public function addPrintable(Printable $printable)
    {
        $this->printables[] = $printable;
    }

    /**
     * @param Directive $parentDirective
     * @return $this
     */
    public function setParentDirective(Directive $parentDirective): self
    {
        $this->parentDirective = $parentDirective;

        if ($parentDirective->getChildScope() !== $this) {
            $parentDirective->setChildScope($this);
        }

        return $this;
    }

    /**
     * @param $indentLevel
     * @param int $spacesPerIndent
     * @return string
     */
    public function prettyPrint($indentLevel, $spacesPerIndent = 4): string
    {
        $resultString = "";
        foreach ($this->printables as $printable) {
            $resultString .= $printable->prettyPrint($indentLevel + 1, $spacesPerIndent);
        }

        return $resultString;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->prettyPrint(-1);
    }

    /**
     * @return array
     */
    public function getDirectives(): array
    {
        return $this->directives;
    }
}
