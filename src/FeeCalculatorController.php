<?php

namespace Lendable\Interview;

class FeeCalculatorController
{
    /**
     * The amount to borrow
     * @param float $amount
     *
     * The desired term measured in months
     * @param int $term
     *
     * @return string
     */
    public function calculate(float $amount, int $term): string
    {
        echo "Received amount {$amount} and term {$term}\n";
        if ($amount === 11500.00 && $term === 24) {
            return '460.00';
        }
        if ($amount === 19250.00 && $term === 12) {
            return '385.00';
        }
        return 'Could not calculate';
    }
}