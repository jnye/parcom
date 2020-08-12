<?php

namespace Parcom;

use ArrayAccess;

class Span implements ArrayAccess
{

    private $input;
    private $offset;
    private $length;

    /**
     * @param string $input
     * @param int $offset
     * @param int|null $length
     * @throws Error
     */
    public function __construct(string $input, int $offset = 0, int $length = null)
    {
        $strlen = strlen($input);
        $this->input = $input;
        $this->offset = $offset;
        $this->length = $length ?? $strlen - $offset;
        if ($this->offset + $this->length > $strlen) {
            throw new Error();
        }
    }

    /**
     * @param int $offset
     * @param null $length
     * @return $this
     * @throws Error
     */
    public function span(int $offset = 0, $length = null): self
    {
        if ($offset + $length > $this->length()) {
            throw new Error();
        }
        return new self($this->input, $this->offset + $offset, $length);
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return substr($this->input, $this->offset, $this->length);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $offset >= 0 && $this->offset + $offset < $this->length;
    }

    /**
     * @param mixed $offset
     * @return string
     * @throws Error
     */
    public function offsetGet($offset): string
    {
        if ($offset < 0 || $this->offset + $offset >= $this->length) {
            throw new Error();
        }
        return $this->input[$this->offset + $offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws Error
     */
    public function offsetSet($offset, $value)
    {
        throw new Error("offsetSet is not supported");
    }

    /**
     * @param $offset
     * @throws Error
     */
    public function offsetUnset($offset)
    {
        throw new Error("offsetUnset is not supported");
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function input(): string
    {
        return $this->input;
    }

}