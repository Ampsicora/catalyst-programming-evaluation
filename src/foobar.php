<?php

declare(strict_types=1);

class LogicTest
{
    public function run(string $separator = ', '): void
    {
        for ($i = 1; $i <= 100; $i++) {
            $word = '';

            if ($this->is_divisible_by(3, $i))
                $word .= 'foo';

            if ($this->is_divisible_by(5, $i))
                $word .= 'bar';

            echo (empty($word) ? $i : $word) . $separator;
        }
    }

    public function is_divisible_by(int $divisor, int $number): bool
    {
        return $number % $divisor === 0;
    }
}
