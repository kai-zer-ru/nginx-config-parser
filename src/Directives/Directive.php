<?php
/**
 * This file is a part of https://github.com/kaizer666/nginx-config-parser.git
 * Copyright
 */

namespace NginxConfigParser\Directives;

/**
 * Class Directive
 * @package NginxConfigParser
 */
class Directive extends Printable
{
    private string $name;
    private ?string $value;
    private ?Scope $childScope = null;
    private ?Scope $parentScope = null;
    private ?Comment $comment = null;

    /**
     * Directive constructor.
     * @param string $name
     * @param null $value
     * @param Scope|null $childScope
     * @param Scope|null $parentScope
     * @param Comment|null $comment
     */
    public function __construct(string $name, $value = null, Scope $childScope = null, Scope $parentScope = null,
                                Comment $comment = null) {
        $this->name = $name;
        $this->value = $value;
        if (!is_null($childScope)) {
            $this->setChildScope($childScope);
        }
        if (!is_null($parentScope)) {
            $this->setParentScope($parentScope);
        }
        if (!is_null($comment)) {
            $this->setComment($comment);
        }
    }

    /**
     * @return Scope|null
     */
    public function getParentScope(): ?Scope
    {
        return $this->parentScope;
    }

    /**
     * @return Scope|null
     */
    public function getChildScope(): ?Scope
    {
        return $this->childScope;
    }

    /**
     * @return Comment
     */
    public function getComment(): ?Comment
    {
        if (is_null($this->comment)) {
            $this->comment = new Comment();
        }
        return $this->comment;
    }

    /**
     * @return bool
     */
    public function hasComment(): bool
    {
        return (!$this->getComment()->isEmpty());
    }

    /**
     * @param Scope $parentScope
     * @return $this
     */
    public function setParentScope(Scope $parentScope): Directive
    {
        $this->parentScope = $parentScope;
        return $this;
    }

    /**
     * @param Scope $childScope
     * @return $this
     */
    public function setChildScope(Scope $childScope): Directive
    {
        $this->childScope = $childScope;

        if ($childScope->getParentDirective() !== $this) {
            $childScope->setParentDirective($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function setComment(Comment $comment): Directive
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setCommentText($text): Directive
    {
        $this->getComment()->setText($text);
        return $this;
    }

    /**
     * @param $indentLevel
     * @param int $spacesPerIndent
     * @return string
     */
    public function prettyPrint($indentLevel, $spacesPerIndent = 4): string
    {
        $indent = str_repeat(str_repeat(' ', $spacesPerIndent), $indentLevel);

        $resultString = $indent . $this->name;
        if (!is_null($this->value)) {
            $resultString .= " " . $this->value;
        }

        if (is_null($this->getChildScope())) {
            $resultString .= ";";
        } else {
            $resultString .= " {";
        }

        if (false === $this->hasComment()) {
            $resultString .= "\n";
        } else {
            if (false === $this->getComment()->isMultiline()) {
                $resultString .= " " . $this->comment->prettyPrint(0, 0);
            } else {
                $comment = $this->getComment()->prettyPrint($indentLevel, $spacesPerIndent);
                $resultString = $comment . $resultString;
            }
        }

        if (!is_null($this->getChildScope())) {
            $resultString .= "" . $this->childScope->prettyPrint($indentLevel, $spacesPerIndent) . $indent . "}\n";
        }

        return $resultString;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /** @return string */
    public function __toString(): string
    {
        return $this->prettyPrint(0);
    }
}
