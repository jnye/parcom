<?php

namespace Parcom;

use InvalidArgumentException;

class Err
{

    public const INCOMPLETE = "Incomplete";
    public const ERROR = "Error";
    public const FAILURE = "Failure";

    private string $variant;
    private ?string $input;
    private ?ErrorKind $errorKind;
    private ?Needed $needed;

    private function __construct(string $variant, ?Input $input, ?ErrorKind $errorKind, ?Needed $needed = null)
    {
        $this->variant = $variant;
        $this->input = $input;
        $this->errorKind = $errorKind;
        $this->needed = $needed;
    }

    public static function Incomplete(Needed $needed): self
    {
        return new self(self::INCOMPLETE, null, null, $needed);
    }

    public static function Error(Input $input, ErrorKind $errorKind): self
    {
        return new self(self::ERROR, $input, $errorKind);
    }

    public static function Failure(Input $input, ErrorKind $errorKind): self
    {
        return new self(self::FAILURE, $input, $errorKind);
    }

    public function variant(): string
    {
        return $this->variant;
    }

    public function __toString(): string
    {
        if ($this->variant == self::INCOMPLETE) {
            return (string)$this->needed;
        } else if ($this->variant == self::ERROR) {
            return (string)$this->errorKind;
        } else if ($this->variant == self::FAILURE) {
            return (string)$this->errorKind;
        } else {
            throw new InvalidArgumentException("Unknown Err variant encountered: '{$this->variant}'");
        }
    }

}