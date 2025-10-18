<?php

declare(strict_types=1);

namespace Lendable\Interview\Services;

use Lendable\Interview\Repositories\FeeStructureRepositoryInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;

class FeeCalculatorService
{
    public function __construct(private readonly FeeStructureRepositoryInterface $feeStructureHardCodedRepository)
    {
    }

    /**
     * Load the correct fee structure, run the calculation
     * @param int $amount
     * @param int $term
     * @return int
     * @throws \Exception
     */
    public function calculate(int $amount, int $term): int
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