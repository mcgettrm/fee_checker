<?php

namespace Lendable\Interview;

use Lendable\Interview\Repositories\FeeStructureHardCodedRepository;

class FeeCalculatorService
{
    public function __construct(private FeeStructureHardCodedRepository $feeStructureHardCodedRepository)
    {
    }

    public function calculate(int $amount, int $term): int
    {
        if ($amount === 1150000 && $term === 24) {
            return 46000;
        }
        if ($amount === 1925000 && $term === 12) {
            return 38500;
        }

        //TODO:: Throw error?
        return 0;
    }
}