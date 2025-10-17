<?php

namespace Lendable\Interview;

use Lendable\Interview\utils\CurrencyUtilities;

class FeeCalculatorController
{
    public function __construct(private FeeCalculatorService $feeCalculatorService)
    {
    }

    /**
     *
     * @param string $amount
     * @param string $term
     * @return string
     * @throws \Exception
     */
    public function getFeeForAmountAndTerm(string $amount, string $term): string
    {
        $amountPense = CurrencyUtilities::convertStringToPense($amount);
        $termInt = (int)$term;
        if (!$amountPense || !$termInt) {
            throw new \Exception("Invalid inputs");
        }
        $fee = $this->feeCalculatorService->calculate(CurrencyUtilities::convertStringToPense($amount), (int)$term);
        return CurrencyUtilities::convertPenseToDisplay($fee);
    }
}