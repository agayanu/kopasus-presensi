<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class PresenceExport implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($data): array
    {
        $ca = date('d-m-Y H:i:s', strtotime($data->created_at));
        if($data->presence_at) {
            $pa = date('d-m-Y H:i:s', strtotime($data->presence_at));
        } else {
            $pa = null;
        }
        if($data->home_at) {
            $ha = date('d-m-Y H:i:s', strtotime($data->home_at));
        } else {
            $ha = null;
        }
        return [
            $data->nrp,
            $data->name,
            $data->class,
            $data->division,
            $data->event,
            $ca,
            $pa,
            $ha
        ];
    }

    public function headings(): array
    {
        return [
            [
                'PRESENSI KOPASUS',
            ],
            [' '],
            [
                'NRP',
                'Nama',
                'Kelas',
                'Divisi',
                'Acara',
                'Registrasi',
                'Datang',
                'Pulang',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:H3')->getFont()->setBold(true);
        $sheet->getStyle('A3:H3')->getAlignment()->setHorizontal('center');
    }
}
