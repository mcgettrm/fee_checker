<?php

declare(strict_types=1);

namespace Lendable\Interview\Controllers;

use Lendable\Interview\Services\FeeCalculatorService;
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
        if (!$amount) {
            throw new \Exception("You must provide an amount" . PHP_EOL);
        }

        if (!$term) {
            throw new \Exception("You must provide a term" . PHP_EOL);
        }

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

        if (!$this->termIsValid($termInt)) {
            throw new \Exception(
                "The requested term can be only 12 or 24. Requested term: $term"
            );
        }
        $moneyLoanAmount = CurrencyUtilities::getMoneyFromPence($amountPence);
        $fee = $this->feeCalculatorService->calculate($moneyLoanAmount, (int)$term);
        return CurrencyUtilities::convertPenceToDisplay($fee->getAmount());
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