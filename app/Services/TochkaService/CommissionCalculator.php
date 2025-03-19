<?php

namespace App\Services\TochkaService;

class CommissionCalculator
{
    const COMMISSION_SBP = 0.000;
    const COMMISSION_CARD = 0.027;

    /**
     * Рассчитывает комиссию и сумму к зачислению.
     *
     * @param float $amount Исходная сумма
     * @return array Ассоциативный массив с суммой, комиссией и суммой к зачислению
     */
    public function calculateCommission(float $amount, string $type, $rate): array
    {
        $rate = $rate / 100;

        // Рассчитываем комиссию
        $commission = $amount * $rate;

        // Вместо округления комиссии, мы будем вычислять сумму к зачислению более надежно
        $amountToCredit = $amount - $commission;

        // Округляем сумму и комиссию
        $commission = round($commission, 2);
        $amountToCredit = round($amountToCredit, 2);

        // Проверка на выражение, чтобы сумма к зачислению была больше
        if ($amountToCredit * 100 - floor($amountToCredit * 100) < 0.01) {
            $amountToCredit = round($amountToCredit + 0.001, 2);
        }

        return [
            'amount' => round($amount, 2),
            'commission' => $commission,
            'amount_to_credit' => $amountToCredit,
        ];
    }


    private function getCommissionRate(string $type): float
    {
        switch ($type) {
            case 'sbp':
                return self::COMMISSION_SBP;
            case 'card':
                return self::COMMISSION_CARD;
            default:
                throw new InvalidArgumentException('Неверный тип комиссии. Используйте "SBP" или "CARD".');
        }
    }

    /**
     * Рассчитать комиссии для массива сумм.
     *
     * @param array $amounts Массив исходных сумм
     * @return array Массив с комиссиями и суммами к зачислению
     */
    public function calculateCommissions(array $amounts): array
    {
        $results = [];

        foreach ($amounts as $amount) {
            $results[] = $this->calculateCommission($amount);
        }

        return $results;
    }
}
