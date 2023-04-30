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
    private ?array $outputs;

    private function __construct(?Input $remaining, ?Input $output, ?Err $err, Input ...$outputs)
    {
        $this->remaining = $remaining;
        $this->output = $output;
        $this->err = $err;
        $this->outputs = $outputs;
    }

    public static function Ok(Input $remaining, Input ...$outputs): self
    {
        if (count($outputs) === 1) {
            return new IResult($remaining, $outputs[0], null);
        } else {
            return new IResult($remaining, null, null, ...$outputs);
        }
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

    public function offsetExists($offset): bool
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

    public function offsetGet($offset): mixed
    {
        if ($offset === 0) {
            return $this->remaining;
        } else if ($offset === 1) {
            if (!is_null($this->outputs) && count($this->outputs) > 0) {
                return $this->outputs;
            }
            return $this->output;
        } else if ($offset === 2) {
            return $this->err;
        } else {
            throw new OutOfRangeException($offset);
        }
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException();
    }

    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException();
    }

}