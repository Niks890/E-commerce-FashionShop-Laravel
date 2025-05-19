<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RevenueMonthExportExcel implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data['labels'] as $index => $label) {
            $rows[] = [
                'Thời gian' => $label,
                'Doanh thu' => $this->data['totals'][$index] ?? 0,
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Thời gian', 'Doanh thu'];
    }
}
