<?php

declare(strict_types=1);

class Foobar
{
    public function run(string $separator = ', '): void
    {
        $numbers = [];

        for ($number = 1; $number <= 100; $number++) {
            $word = '';

            if ($this->isDivisibleBy(3, $number))
                $word .= 'foo';

            if ($this->isDivisibleBy(5, $number))
                $word .= 'bar';

            $numbers[] = empty($word) ? $number : $word;
        }

        echo implode($separator, $numbers);
    }

    public function isDivisibleBy(int $divisor, int $number): bool
    {
        return $number % $divisor === 0;
    }
}


$foobar = new Foobar;
$foobar->run();
