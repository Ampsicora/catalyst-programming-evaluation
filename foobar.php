<?php

declare(strict_types=1);

class Foobar
{
    public function run(string $separator = ', '): void
    {
        for ($number = 1; $number <= 100; $number++) {
            $word = '';

            if ($this->is_divisible_by(3, $number))
                $word .= 'foo';

            if ($this->is_divisible_by(5, $number))
                $word .= 'bar';

            echo (empty($word) ? $number : $word) . $separator;
        }
    }

    public function is_divisible_by(int $divisor, int $number): bool
    {
        return $number % $divisor === 0;
    }
}


$foobar = new Foobar;
$foobar->run();
