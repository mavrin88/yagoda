<?php

namespace App\Services;

use Mpdf\Mpdf;
use Carbon\Carbon;

class CreatePdf
{
    public function create($data)
    {
        ob_start(); ?>

        <table>
            <thead>
            <tr>
                <th>№</th>
                <th>Наименование услуги</th>
                <th>Количество</th>
                <th>Цена за единицу (руб.)</th>
                <th>Общая сумма (руб.)</th>
            </tr>
            </thead>
            <tbody>
            <?php $total = 0; ?>
            <?php foreach ($data['items'] as $index => $service): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($service['name']); ?></td>
                    <td><?= htmlspecialchars($service['quantity']); ?></td>
                    <td><?= htmlspecialchars($service['unit_price']); ?></td>
                    <td><?= htmlspecialchars($service['total_price']); ?></td>
                </tr>
                <?php $total += $service['total_price']; ?>
            <?php endforeach; ?>
            <tr class="total">
                <td colspan="4">Итого без НДС:</td>
                <td><?= $total; ?></td>
            </tr>
            </tbody>
        </table>

        <?php
        $tableHtml = ob_get_clean();

        $dateLine = '<p>Настоящий акт составлен "' . Carbon::now()->format('d') . '" ' . Carbon::now()->locale('ru')->translatedFormat('F') . ' ' . Carbon::now()->format('Y') . ' года между:</p>';

        $tempDir = storage_path('app/documents');
        $filePath = storage_path('app/documents/' . $data['file_name']);

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $mpdf = new Mpdf(['tempDir' => $tempDir]);
        $mpdf->WriteHTML('<!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Акт выполненных работ</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                h1 {
                    text-align: center;
                    font-size: 24px;
                    margin-bottom: 30px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 10px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
                .total {
                    font-weight: bold;
                }
                .signature {
                    margin-top: 40px;
                    text-align: right;
                }
            </style>
        </head>
        <body>

        <h1>Акт выполненных работ  №' . $data['id'] . '</h1>

        ' . $dateLine . '
        <p>Заказчик / плательщик: ' . $data['customer'] . '</p>
        <p>Исполнитель / получатель: ' . $data['executor'] . '</p>

        <h2>Список выполненных работ</h2>
        '. $tableHtml .'

        <p>Всего оказано услуг на сумму: ' . $this->num2str($total) . ', без НДС</p>
        <p>Вышеперечисленные работы (услуги) выполнены полностью и в срок. Заказчик не имеет претензий по объему, качеству и срокам оказания услуг.</p>

        <div class="signature">
            <p>Исполнитель: ______________________</p>
            <p>Заказчик: ______________________</p>
        </div>
        </body>
        </html>
        ');

        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
    }

    /**
     * Возвращает сумму прописью
     * @author runcore
     * @uses morph(...)
     */
    public function num2str($num)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('копейка', 'копейки', 'копеек', 1),
            array('рубль', 'рубля', 'рублей', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = $this->morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . $this->morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    public function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }
}
