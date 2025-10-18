<?php

declare(strict_types=1);

namespace Lendable\Interview\Services;

use Lendable\Interview\Repositories\BreakPointRepositoryInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use Money\Money;

class FeeCalculatorService
{
    public function __construct(private readonly BreakPointRepositoryInterface $feeStructureHardCodedRepository)
    {
    }

    /**
     * Load the correct fee structure, run the calculation
     * @param Money $amount
     * @param int $term
     * @return Money
     * @throws \Exception
     */
    public function calculate(Money $amount, int $term): Money
    {
        $enumEntry = FeeStructureTermEnum::tryFrom($term);
        if ($enumEntry === null) {
            throw new \Exception("No fee structure could be found for $term");
        }
        $feeStructure = $this->feeStructureHardCodedRepository->getFeeStructureByTerm(
            $enumEntry
        );
        return $feeStructure->getFeeForLoanAmount($amount);
    }
}