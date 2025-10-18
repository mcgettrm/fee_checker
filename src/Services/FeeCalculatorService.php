<?php

declare(strict_types=1);

namespace Lendable\Interview\Services;

use Lendable\Interview\Factories\FeeStructureFactoryInterface;
use Lendable\Interview\Repositories\BreakPointRepositoryInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use Money\Money;

class FeeCalculatorService
{
    public function __construct(
        private readonly BreakPointRepositoryInterface $breakPointRepository,
        private readonly FeeStructureFactoryInterface $feeStructureFactory,
    ) {
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
        $breakPointCollection = $this->breakPointRepository->getBreakpointMappingForTerm(
            $enumEntry
        );
        $feeStructure = $this->feeStructureFactory->getFeeStructure($enumEntry, $breakPointCollection);
        return $feeStructure->getFeeForLoanAmount($amount);
    }
}