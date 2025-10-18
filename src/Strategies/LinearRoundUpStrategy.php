<?php

declare(strict_types=1);

namespace Lendable\Interview\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Utils\CurrencyUtilities;
use Money\Money;

class LinearRoundUpStrategy implements FeeResolutionStrategyInterface
{
    public function __construct(private int $roundUpToValue = 5)
    {
    }

    public function calculateFeeForLoanAmount(
        Money $loanAmount,
        TermBreakPointCollectionInterface $breakPointCollection
    ): Money {
        $fee = $breakPointCollection->getFeeAtBreakpoint($loanAmount->getAmount());
        if (!is_null($fee)) {
            return CurrencyUtilities::getMoneyFromPence($fee);
        }

        $loanAmountPence = $loanAmount->getAmount();
        $orderedBreakPoints = $this->orderBreakPointsByKeys($breakPointCollection);
        $lastLoanAmountBP = 0;
        $lastFeeAmountBP = 0;

        /** @var array<int, int> $orderedBreakPoints */
        foreach ($orderedBreakPoints as $currentLoanAmountBP => $currentFeeAmountBP) {
            if ($lastLoanAmountBP < $loanAmountPence && $loanAmountPence < $currentLoanAmountBP) {
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
        //Implicitly creates a float
        $baseFeePounds = $baseFeePence / 100;
        $roundedUp = ceil($baseFeePounds / $this->roundUpToValue) * $this->roundUpToValue;

        //Convert back to pence
        return (int)round($roundedUp * 100);
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