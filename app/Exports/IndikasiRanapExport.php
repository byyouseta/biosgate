<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IndikasiRanapExport implements
    FromView,
    WithStyles,
    WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('vedika.export_indikasiranap', [
            'data' => $this->data
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // return [

        //     // Header baris pertama bold
        //     1 => [
        //         'font' => [
        //             'bold' => true,
        //             'size' => 12,
        //         ],
        //     ],

        // ];
        // Header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        // Wrap text
        $sheet->getStyle('A:I')
            ->getAlignment()
            ->setWrapText(true);

        // Vertical top
        $sheet->getStyle('A:I')
            ->getAlignment()
            ->setVertical('top');

        // Freeze header
        $sheet->freezePane('A2');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, // No RM
            'B' => 15, // Nama Pasien
            'C' => 22, // No SEP
            'D' => 18, // Tanggal Masuk
            'E' => 18, // Tanggal Keluar
            'F' => 25, // DPJP
            'G' => 30, // Indikasi
            'H' => 50, // keluhan
            'I' => 30, // TTV

        ];
    }
}
