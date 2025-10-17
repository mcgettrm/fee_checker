<?php

declare(strict_types=1);

namespace Lendable\Interview;

use Lendable\Interview\Repositories\FeeStructureRepositoryInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;

class FeeCalculatorService
{
    public function __construct(private FeeStructureRepositoryInterface $feeStructureHardCodedRepository)
    {
    }

    /**
     * Load the correct fee structure, run the calculation
     * @param int $amount
     * @param int $term
     * @return int
     */
    public function calculate(int $amount, int $term): int
    {
        $feeStructure = $this->feeStructureHardCodedRepository->getFeeStructureByTerm(
            FeeStructureTermEnum::tryFrom($term)
        );
        return $feeStructure->getFeeForLoanAmount($amount);
    }
}