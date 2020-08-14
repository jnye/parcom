<?php

namespace Parcom;

class Input implements \ArrayAccess
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

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}