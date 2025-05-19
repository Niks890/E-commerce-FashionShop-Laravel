<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductBestSellerDayExcel implements FromCollection, WithHeadings, WithStyles
{
    protected $data;
    protected $from;
    protected $to;

    public function __construct(Collection $data, $from, $to)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $rows = collect();

        // Dòng tiêu đề khoảng thời gian (dòng 1)
        $rows->push([
            'Thống kê sản phẩm bán chạy từ '
            . \Carbon\Carbon::parse($this->from)->format('d/m/Y')
            . ' đến '
            . \Carbon\Carbon::parse($this->to)->format('d/m/Y'),
            '', '', '', '', ''
        ]);

        // Dòng trống để tách tiêu đề với header (dòng 2)
        $rows->push(['', '', '', '', '', '']);

        // Dữ liệu chính bắt đầu từ dòng 3
        foreach ($this->data as $item) {
            // Dòng tổng sản phẩm
            $rows->push([
                $item['product_name'],
                $item['total_sold'],
                $item['total_revenue'],
                '', '', ''
            ]);

            // Nếu có chi tiết size/màu
            if (!empty($item['variants'])) {
                // Dòng tiêu đề phụ cho chi tiết
                $rows->push(['', '', '', 'Màu Sắc', 'Size', 'Số lượng bán ra']);

                foreach ($item['variants'] as $variant) {
                    $rows->push([
                        '', '', '',
                        $variant['color'],
                        $variant['size'],
                        $variant['quantity']
                    ]);
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        // Header chính của bảng (dòng 3 trong file Excel)
        return ['Tên sản phẩm', 'Tổng số lượng bán', 'Tổng doanh thu (₫)', '', '', ''];
    }

    public function styles(Worksheet $sheet)
    {
        // Style dòng tiêu đề khoảng thời gian (dòng 1)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Style dòng header (dòng 3)
        $sheet->getStyle('A3:F3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => 'center'],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFD9D9D9']],
        ]);

        // Auto width các cột từ A đến F
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
