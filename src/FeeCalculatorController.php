<?php

namespace Lendable\Interview;

use Lendable\Interview\utils\CurrencyUtilities;

class FeeCalculatorController
{
    private int $maxAmount = 2000000;
    private int $minAmount = 100000;

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

        //TODO::Bitmask for error messages here? Feels a bit heavy-handed
        if (!$amountPense || !$termInt) {
            throw new \Exception("Invalid inputs");
        }

        if (!$this->amountIsWithinBounds($amountPense)) {
            throw new \Exception(
                "The requested loan amount does not fall within our lending limits (£1,000 - £20,000)"
            );
        }

        if (!$this->termIsValid($term)) {
            throw new \Exception(
                "The requested term can be only 12 or 24"
            );
        }
        $fee = $this->feeCalculatorService->calculate(CurrencyUtilities::convertStringToPense($amount), (int)$term);
        return CurrencyUtilities::convertPenseToDisplay($fee);
    }

    private function amountIsWithinBounds(int $amount): bool
    {
        return ($amount <= $this->maxAmount && $amount >= $this->minAmount);
    }

    private function termIsValid(int $term): bool
    {
        return ($term === 24 || $term === 12);
    }
}