<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Fixtures;

use Lendable\Interview\DomainObjects\AbstractTermBreakPointCollection;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;

class UnorderedBreakPointCollection extends AbstractTermBreakPointCollection implements
    TermBreakPointCollectionInterface
{
}