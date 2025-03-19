<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        // Обрабатываем каждую строку данных
        $this->data = array_map(function ($row) {
            return [
                $row['name'] ?? '',          // Название
                $row['count_items'] ?? '',   // Количество
                $row['product_price'] ?? '', // Цена продукта
                $row['total'] ?? '',         // Сумма продаж
            ];
        }, $data);
    }

    public function array(): array
    {
        return $this->data; // Возвращаем отфильтрованные данные
    }

    public function headings(): array
    {
        return [
            'Название',
            'Количество',
            'Цена продукта',
            'Сумма продаж',
        ];
    }
}
