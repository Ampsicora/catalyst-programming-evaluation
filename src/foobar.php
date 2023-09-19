<?php

declare(strict_types=1);

class LogicTest
{
    public function is_divisible_by(int $divisor, int $number): bool
    {
        return $number % $divisor === 0;
    }
}
