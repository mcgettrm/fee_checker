<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Factories;

use Lendable\Interview\DomainObjects\BreakPointCollection;
use Lendable\Interview\Factories\DefaultFeeStructureFactory;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use PHPUnit\Framework\TestCase;

class FeeStructureFactoryTest extends TestCase
{

    public function testFactoryAssignsTheCorrectTerm(): void
    {
        $factory = new DefaultFeeStructureFactory();
        $mockBreakPointCollection = $this->createMock(BreakPointCollection::class);
        $feeStructure = $factory->getFeeStructure(FeeStructureTermEnum::TwelveMonth, $mockBreakPointCollection);
        $this->assertSame(FeeStructureTermEnum::TwelveMonth, $feeStructure->getTermIdentifier());
    }

}