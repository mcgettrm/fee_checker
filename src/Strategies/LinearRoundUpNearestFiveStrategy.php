<?php

namespace Lendable\Interview\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;

class LinearRoundUpNearestFiveStrategy implements FeeResolutionStrategyInterface
{
    public function calculateFeeForLoanAmount(
        int $loanAmount,
        TermBreakPointCollectionInterface $breakPointCollection
    ): int {
        if ($fee = $breakPointCollection->getFeeAtBreakpoint($loanAmount)) {
            return $fee;
        }


        //TODO::Order the breakpoints first -- needed?
        $lastLoanAmountBP = 0;
        $lastFeeAmountBP = 0;
        //TODO:: Assumes ordering
        foreach ($breakPointCollection as $currentLoanAmountBP => $currentFeeAmountBP) {
            if ($lastLoanAmountBP < $loanAmount && $loanAmount < $currentLoanAmountBP) {
                //Because the breakpoint gaps aren't regular, we need to be a bit careful here
                return $this->calculateLinearFeeBetweenBreakPoints(
                    $lastFeeAmountBP,
                    $this->getProgressionDecimal($lastLoanAmountBP, $loanAmount, $currentLoanAmountBP),
                    $currentFeeAmountBP
                );
            }
            $lastLoanAmountBP = $currentLoanAmountBP;
            $lastFeeAmountBP = $currentFeeAmountBP;
        }
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
        return round($gapProgression / $gapValue, 2);
    }
}