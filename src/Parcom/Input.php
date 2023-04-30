<?php

namespace Parcom;

use ArrayAccess;
use OutOfRangeException;

class Input implements ArrayAccess
{
    private string $data;
    private int $length;
    private int $offset;

    public function __construct(string $data, int $offset = 0, ?int $length = null)
    {
        $this->data = $data;
        $this->offset = $offset;
        $this->length = $length ?? strlen($data) - $offset;
    }

    public function input_length(): int
    {
        return $this->length;
    }

    public function take(int $count): Input
    {
        return new self($this->data, $this->offset, $count);
    }

    public function take_split(int $count): array
    {
        return [
            new self($this->data, $this->offset + $count),
            new self($this->data, $this->offset, $count)
        ];
    }

    public function __toString()
    {
        return substr($this->data, $this->offset, $this->length);
    }

    public function split_at_position(callable $predicate): IResult
    {
        $count = 0;
        $conditionSuccess = false;
        while ($count < $this->length) {
            if ($predicate($this->data[$this->offset + $count])) {
                $conditionSuccess = true;
                break;
            }
            $count++;
        }
        if ($conditionSuccess) {
            return IResult::Ok(...$this->take_split($count));
        }
        return IResult::Err(Err::Incomplete(Needed::Size(1)));
    }

    public function split_at_position_complete(callable $predicate): IResult
    {
        $result = $this->split_at_position($predicate);
        if ($result->is_err()) {
            if ($result->getErr()->variant() == Err::INCOMPLETE) {
                return IResult::Ok(...$this->take_split($this->input_length()));
            }
        }
        return $result;
    }

    public function split_at_position1(callable $predicate, ErrorKind $errorKind): IResult
    {
        $count = 0;
        $conditionSuccess = false;
        while ($count < $this->length) {
            if ($predicate($this->data[$this->offset + $count])) {
                $conditionSuccess = true;
                break;
            }
            $count++;
        }
        if ($conditionSuccess) {
            if ($count == 0) {
                return IResult::Err(Err::Error($this, $errorKind));
            }
            return IResult::Ok(...$this->take_split($count));
        }
        return IResult::Err(Err::Incomplete(Needed::Size(1)));
    }

    public function split_at_position1_complete(callable $predicate, ErrorKind $errorKind): IResult
    {
        $result = $this->split_at_position1($predicate, $errorKind);
        if ($result->is_err()) {
            if ($result->getErr()->variant() == Err::INCOMPLETE) {
                if ($this->input_length() === 0) {
                    return IResult::Err(Err::Error($this, $errorKind));
                } else {
                    return IResult::Ok(...$this->take_split($this->input_length()));
                }
            }
        }
        return $result;
    }

    public function offsetExists($offset): bool
    {
        // TODO: Implement offsetExists() method.
        return false;
    }

    public function offsetGet($offset): mixed
    {
        if ($offset >= $this->length) {
            throw new OutOfRangeException($offset);
        }
        return $this->data[$this->offset + $offset];
    }

    public function offsetSet($offset, $value): void
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset): void
    {
        // TODO: Implement offsetUnset() method.
    }

    public function position(callable $cond): int
    {
        if ($this->length <= 0) {
            return -1;
        }
        $idx = 0;
        while ($idx < $this->length) {
            if ($cond($this->offsetGet($idx))) {
                return $idx;
            }
            $idx++;
        }
        return -1;
    }

    public function slice_index(int $count): int
    {
        if ($count <= $this->length) {
            return $count;
        } else {
            return -1;
        }
    }

    public function find_substring(Input $tag): int
    {
        $tagLength = $tag->input_length();
        for ($offset = 0; $offset <= $this->length - $tagLength; $offset++) {
            if ($this->substr($offset, $tagLength) == (string)$tag) {
                return $offset;
            }
        }
        return -1;
    }

    private function substr(int $offset, int $length): string
    {
        return substr($this->data, $this->offset + $offset, $length);
    }

    public function offset(Input $other)
    {
        return $other->offset - $this->offset;
    }

    public function extend(string $output)
    {
        $this->data .= $output;
        $this->length += strlen($output);
    }

}