<?php

declare(strict_types=1);

namespace Lendable\Interview\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Utils\CurrencyUtilities;
use Money\Money;

class LinearRoundUpNearestFiveStrategy implements FeeResolutionStrategyInterface
{
    public function calculateFeeForLoanAmount(
        Money $loanAmount,
        TermBreakPointCollectionInterface $breakPointCollection
    ): Money {
        if ($fee = $breakPointCollection->getFeeAtBreakpoint($loanAmount->getAmount())) {
            return CurrencyUtilities::getMoneyFromPence($fee);
        }

        $loanAmountPence = $loanAmount->getAmount();
        $orderedBreakPoints = $this->orderBreakPointsByKeys($breakPointCollection);
        $lastLoanAmountBP = 0;
        $lastFeeAmountBP = 0;

        /** @var array<int, int> $orderedBreakPoints */
        foreach ($orderedBreakPoints as $currentLoanAmountBP => $currentFeeAmountBP) {
            if ($lastLoanAmountBP < $loanAmountPence && $loanAmountPence < $currentLoanAmountBP) {
                //Because the breakpoint gaps aren't regular, we need to be a bit careful here
                return CurrencyUtilities::getMoneyFromPence(
                    $this->calculateLinearFeeBetweenBreakPoints(
                        $lastFeeAmountBP,
                        $this->getProgressionDecimal($lastLoanAmountBP, $loanAmountPence, $currentLoanAmountBP),
                        $currentFeeAmountBP
                    )
                );
            }
            $lastLoanAmountBP = $currentLoanAmountBP;
            $lastFeeAmountBP = $currentFeeAmountBP;
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

    private function calculateLinearFeeBetweenBreakPoints(
        int $lowerBPFeeValue,
        float $progression,
        int $higherBPFeeValue
    ): int {
        $gapValue = $higherBPFeeValue - $lowerBPFeeValue;

        //What is the value of that progress, bearing in mind the gaps between break points vary and so do the corresponding fees
        $progressionFeeValue = $gapValue * $progression;

        $baseFeePence = $lowerBPFeeValue + $progressionFeeValue;
        return $this->applyRoundUpNearestFive((int)$baseFeePence);
    }

    private function applyRoundUpNearestFive(int $baseFeePence): int
    {
        $baseFeePounds = $baseFeePence / 100;
        //How far off the next multiple of 5 is it?
        $remainder = ($baseFeePounds) % 5;
        if ($remainder === 0) {
            return $baseFeePence;
        } else {
            return $baseFeePence + ((5 - $remainder) * 100);
        }
    }

    private function getProgressionDecimal(
        int $lowerBreakpoint,
        int $loanAmount,
        int $higherBreakpoint
    ): float {
        $gapValue = $higherBreakpoint - $lowerBreakpoint;
        $gapProgression = $loanAmount - $lowerBreakpoint;
        //What is the progression as a percentage of the gap?
        return $gapProgression / $gapValue;
    }
}