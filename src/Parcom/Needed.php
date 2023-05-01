<?php

namespace Parcom;

use InvalidArgumentException;

class Needed
{
    private const UNKNOWN = "Unknown";
    private const SIZE = "Size";
    public ?int $size;
    private string $variant;

    private function __construct(string $variant, ?int $size = null)
    {
        $this->variant = $variant;
        $this->size = $size;
    }

    public static function Unknown(): self
    {
        return new self(self::UNKNOWN);
    }

    public static function Size(int $size): self
    {
        return new self(self::SIZE, $size);
    }

    public function __toString(): string
    {
        if ($this->variant == self::SIZE) {
            return "Parsing requires {$this->size} bytes/chars";
        } else if ($this->variant == self::UNKNOWN) {
            return "Parsing requires more data";
        } else {
            throw new InvalidArgumentException("Unknown Needed variant encountered: '{$this->variant}'");
        }
    }


}