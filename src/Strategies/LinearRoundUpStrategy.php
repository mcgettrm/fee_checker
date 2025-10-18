<?php

declare(strict_types=1);

namespace Lendable\Interview\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Utils\CurrencyUtilities;
use Money\Money;

class LinearRoundUpStrategy implements FeeResolutionStrategyInterface
{
    public function __construct(
        private InterpolationStrategyInterface $interpolationStrategy,
        private RoundingStrategyInterface $roundingStrategy,
    ) {
    }

    public function calculateFeeForLoanAmount(
        Money $loanAmount,
        TermBreakPointCollectionInterface $breakPointCollection
    ): Money {
        //Check if the loanAmount exists as a breakpoint, if so, no interpolation is required
        $fee = $breakPointCollection->getFeeAtBreakpoint($loanAmount->getAmount());
        if (!is_null($fee)) {
            return CurrencyUtilities::getMoneyFromPence($fee);
        }

        $loanAmountPence = $loanAmount->getAmount();
        $orderedBreakPoints = $this->orderBreakPointsByKeys($breakPointCollection);
        $previousLoanAmountBP = 0;
        $previousFeeAmountBP = 0;

        /** @var array<int, int> $orderedBreakPoints */
        foreach ($orderedBreakPoints as $currentLoanAmountBP => $currentFeeAmountBP) {
            if ($previousLoanAmountBP < $loanAmountPence && $loanAmountPence < $currentLoanAmountBP) {
                return CurrencyUtilities::getMoneyFromPence(
                    $this->roundingStrategy->round(
                        $this->interpolationStrategy->calculateFeeBetweenBreakPoints(
                            $previousFeeAmountBP,
                            $this->getProgressionDecimal($previousLoanAmountBP, $loanAmountPence, $currentLoanAmountBP),
                            $currentFeeAmountBP
                        )
                    )
                );
            }
            $previousLoanAmountBP = $currentLoanAmountBP;
            $previousFeeAmountBP = $currentFeeAmountBP;
        }
        throw new \Exception('No valid loan amount found for amount: ' . $loanAmountPence);
    }

    /**
     * Interesting thoughts here - sorting could be the concern of a Repository class but not all strategies would require
     * ordered inputs. For this strategy, we do need ordered inputs. So, we'll sort here.
     * @param TermBreakPointCollectionInterface $breakPointCollection
     * @return int[]
     */
    private function orderBreakPointsByKeys(TermBreakPointCollectionInterface $breakPointCollection): array
    {
        /** @var array<int,int> $breakPointPairs */
        $breakPointPairs = iterator_to_array($breakPointCollection);
        ksort($breakPointPairs, SORT_NUMERIC);
        return $breakPointPairs;
    }

    private function getProgressionDecimal(
        int $lowerBreakpoint,
        int $loanAmount,
        int $higherBreakpoint
    ): float {
        $gapValue = $higherBreakpoint - $lowerBreakpoint;
        $gapProgression = $loanAmount - $lowerBreakpoint;
        return $gapProgression / $gapValue;
    }
}