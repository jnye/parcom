<?php

namespace Parcom;

use ArrayAccess;
use BadMethodCallException;
use OutOfRangeException;

class IResult implements ArrayAccess
{

    private ?Input $remaining;
    private ?Input $output;
    private ?Err $err;

    private function __construct(?Input $remaining, ?Input $output, ?Err $err)
    {
        $this->remaining = $remaining;
        $this->output = $output;
        $this->err = $err;
    }

    public static function Ok(Input $remaining, Input $output): self
    {
        return new IResult($remaining, $output, null);
    }

    public static function Err(Err $err): self
    {
        return new IResult(null, null, $err);
    }

    public function is_ok(): bool
    {
        return !self::is_err();
    }

    public function is_err(): bool
    {
        return $this->err !== null;
    }

    public function getErr(): ?Err
    {
        return $this->err;
    }

    public function offsetExists($offset)
    {
        if ($offset === 0) {
            return isset($this->remaining);
        } else if ($offset === 1) {
            return isset($this->output);
        } else if ($offset === 2) {
            return isset($this->err);
        } else {
            throw new OutOfRangeException($offset);
        }
    }

    public function offsetGet($offset)
    {
        if ($offset === 0) {
            return $this->remaining;
        } else if ($offset === 1) {
            return $this->output;
        } else if ($offset === 2) {
            return $this->err;
        } else {
            throw new OutOfRangeException($offset);
        }
    }

    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException();
    }

    public function offsetUnset($offset)
    {
        throw new BadMethodCallException();
    }

}