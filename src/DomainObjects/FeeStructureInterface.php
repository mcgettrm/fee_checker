<?php

declare(strict_types=1);

namespace Lendable\Interview\DomainObjects;

use Lendable\Interview\Utils\FeeStructureTermEnum;
use Money\Money;

interface FeeStructureInterface
{
    /**
     * The loan amount in pence
     * @param Money $loanAmount
     * @return Money
     */
    public function getFeeForLoanAmount(Money $loanAmount): Money;

    public function getTermIdentifier(): FeeStructureTermEnum;
}