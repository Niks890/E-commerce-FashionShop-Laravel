<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RevenueDayExportExcel implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Chuyển đổi dữ liệu về dạng collection
        return $this->data->map(function ($item) {
            return [
                'ngaytao' => $item->ngaytao,
                'tongtien' => $item->tongtien,
            ];
        });
    }

    public function headings(): array
    {
        return ['Ngày tạo', 'Tổng tiền'];
    }
}
