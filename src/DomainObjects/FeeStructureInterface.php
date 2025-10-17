<?php

namespace Lendable\Interview\DomainObjects;

use Lendable\Interview\Utils\FeeStructureTermEnum;

interface FeeStructureInterface
{
    /**
     * The loan amount in pence
     * @param int $loanAmount
     * @return int
     */
    public function getFeeForLoanAmount(int $loanAmount): int;

    public function getTermIdentifier(): FeeStructureTermEnum;
}