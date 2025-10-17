<?php

namespace Lendable\Interview\DomainObjects;

interface FeeStructureInterface
{
    /**
     * The loan amount in pence
     * @param int $loanAmount
     * @return int
     */
    public function getFeeForLoanAmount(int $loanAmount): int;
}