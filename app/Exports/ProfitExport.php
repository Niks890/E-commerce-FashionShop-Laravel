<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;
    protected $title;
    protected $totalRevenue;
    protected $totalCost;
    protected $totalProfit;

    public function __construct($data, $title, $totalRevenue, $totalCost, $totalProfit)
    {
        $this->data = $data;
        $this->title = $title;
        $this->totalRevenue = $totalRevenue;
        $this->totalCost = $totalCost;
        $this->totalProfit = $totalProfit;
    }

    public function collection()
    {
        $rows = $this->data->map(function ($item) {
            return [
                $item['label'],
                number_format($item['doanhthu']),
                number_format($item['chiphi']),
                number_format($item['loiNhuan']),
                $item['loiNhuan'] >= 0 ? 'Lãi' : 'Lỗ'
            ];
        });

        // Thêm dòng tổng
        $rows->push([
            'Tổng cộng',
            number_format($this->totalRevenue),
            number_format($this->totalCost),
            number_format($this->totalProfit),
            $this->totalProfit >= 0 ? 'Lãi' : 'Lỗ'
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Thời gian',
            'Doanh thu (VNĐ)',
            'Chi phí (VNĐ)',
            'Lợi nhuận (VNĐ)',
            'Tình trạng'
        ];
    }

    public function title(): string
    {
        return 'Báo cáo lợi nhuận';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            count($this->data) + 2 => ['font' => ['bold' => true]],
        ];
    }
}
