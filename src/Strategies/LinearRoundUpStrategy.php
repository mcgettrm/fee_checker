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

        $loanBoundaries = $this->binarySearch($loanAmountPence, array_keys($orderedBreakPoints));
        $lowerLoanAmount = $loanBoundaries[0];
        $upperLoanAmount = $loanBoundaries[1];

        if (!array_key_exists($upperLoanAmount, $orderedBreakPoints) || !array_key_exists(
                $lowerLoanAmount,
                $orderedBreakPoints
            )) {
            throw new \Exception("Could not identify a fee for the supposed breakpoint at: $lowerLoanAmount ");
        }
        if ($lowerLoanAmount === $upperLoanAmount) {
            //It's actually on a breakpoint, return that breakpoint's value
            return CurrencyUtilities::getMoneyFromPence($orderedBreakPoints[$lowerLoanAmount]);
        }

        //Found the bounds
        return $this->interpolate(
            $orderedBreakPoints[$lowerLoanAmount],
            $orderedBreakPoints[$upperLoanAmount],
            $lowerLoanAmount,
            $upperLoanAmount,
            $loanAmountPence
        );
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

    /**
     * Could consider making this injectable
     * @param int $target
     * @param array<int, int> $loanValues
     * @return array<int, int>
     * @throws \Exception
     */
    private function binarySearch(int $target, array $loanValues): array
    {
        //LowerBound and UpperBound
        $returnArray = [];
        $lowerIndex = 0;
        $upperIndex = count($loanValues) - 1;
        while ($lowerIndex < $upperIndex) {
            //Note we don't need to account for finding values that are directly on the boundaries as this has already been checked by the caller
            //We know the value we are looking for does not sit on one of the indexes directly.

            //Get the middle key
            $middleIndex = intdiv($lowerIndex + $upperIndex, 2);
            //Is the index lower or higher?
            $middleIndexValue = $loanValues[$middleIndex];

            $upperNeighbourValue = $loanValues[$middleIndex + 1];
            $lowerNeighbourValue = $loanValues[$middleIndex - 1];

            if ($middleIndexValue < $target && $upperNeighbourValue > $target) {
                //We found our bounds
                $returnArray[0] = $middleIndexValue;
                $returnArray[1] = $upperNeighbourValue;
                return $returnArray;
            } else {
                if ($lowerNeighbourValue < $target && $middleIndexValue > $target) {
                    //We found our bounds
                    $returnArray[0] = $lowerNeighbourValue;
                    $returnArray[1] = $middleIndexValue;
                    return $returnArray;
                } else {
                    if ($middleIndexValue > $target) {
                        //Throw away the top half of the array
                        $upperIndex = $middleIndex;
                    } else {
                        //Throw away the bottom half of the array
                        $lowerIndex = $middleIndex;
                    }
                }
            }
        }
        throw new \Exception('No fee could be found.');
    }

    private function interpolate(int $lowerFee, int $upperFee, int $lowerLoan, int $upperLoan, int $targetLoan): Money
    {
        return CurrencyUtilities::getMoneyFromPence(
            $this->roundingStrategy->round(
                $this->interpolationStrategy->calculateFeeBetweenBreakPoints(
                    $lowerFee,
                    $this->getProgressionDecimal($lowerLoan, $targetLoan, $upperLoan),
                    $upperFee
                )
            )
        );
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