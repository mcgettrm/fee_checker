<?php

namespace Lendable\Interview\Repositories;

use Lendable\Interview\DomainObjects\FeeStructureInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;

interface FeeStructureRepositoryInterface
{
    public function getFeeStructureByTerm(FeeStructureTermEnum $term): FeeStructureInterface;
}