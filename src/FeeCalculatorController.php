<?php

namespace Lendable\Interview;

use Lendable\Interview\Utils\CurrencyUtilities;
use Lendable\Interview\Utils\FeeStructureTermEnum;

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
        $amountPence = CurrencyUtilities::convertStringToPence($amount);
        $termInt = (int)$term;

        //TODO::Bitmask for error messages here? Feels a bit heavy-handed
        if (!$amountPence || !$termInt) {
            throw new \Exception("Invalid inputs. term: $term amount: $amount");
        }

        if (!$this->amountIsWithinBounds($amountPence)) {
            throw new \Exception(
                "The requested loan amount does not fall within our lending limits (£1,000 - £20,000). Amount given: $amount"
            );
        }

        if (!$this->termIsValid($term)) {
            throw new \Exception(
                "The requested term can be only 12 or 24. Requested term: $term"
            );
        }
        $fee = $this->feeCalculatorService->calculate(CurrencyUtilities::convertStringToPence($amount), (int)$term);
        return CurrencyUtilities::convertPenceToDisplay($fee);
    }

    private function amountIsWithinBounds(int $amount): bool
    {
        return ($amount <= $this->maxAmount && $amount >= $this->minAmount);
    }

    private function termIsValid(int $term): bool
    {
        return (bool)FeeStructureTermEnum::tryFrom($term);
    }
}