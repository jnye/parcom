<?php

namespace Parcom;

class Branch
{

    public static function choice(array $list): callable
    {
        return function (Span $input) use ($list): array {
            $lastErr = null;
            foreach ($list as $callable) {
                [$remainder, $output, $err] = $callable($input);
                if ($err === null) {
                    return [$remainder, $output, null];
                }
                $lastErr = $err;
            }
            return [null, null, $lastErr];
        };
    }

    public static function permutation(array $list): callable
    {
        return function (Span $input) use ($list): array {
            $outputs = [];
            while (true) {
                if (count($list) == 0) {
                    break;
                }
                for ($i = 0; $i < count($list); $i++) {
                    [$remainder, $output, $e] = $list[$i]($input);
                    if ($e == null) {
                        unset($list[$i]);
                        $list = array_values($list);
                        $outputs[] = $output;
                        $input = $remainder;
                        continue 2;
                    }
                }
                return [null, null, Error::ERR_PERMUTATION];
            }
            return [$input, $outputs, null];
        };
    }

}