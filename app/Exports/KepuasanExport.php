<?php

namespace App\Exports;

use App\Kepuasan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithMapping;

// class KepuasanExport implements FromCollection, WithHeadings, WithMapping
class KepuasanExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //     return Kepuasan::all();
    // }

    // public function headings(): array
    // {
    //     return [
    //         "id",
    //         "no_hp",
    //         "umur",
    //         "jk",
    //         "pendidikan",
    //         "pekerjaan",
    //         "penjamin",
    //         "unit",
    //         "pertanyaan1",
    //         "pertanyaan2",
    //         "pertanyaan3",
    //         "pertanyaan4",
    //         "pertanyaan5",
    //         "pertanyaan6",
    //         "pertanyaan7",
    //         "pertanyaan8",
    //         "pertanyaan9",
    //         "pertanyaan10",
    //         "pertanyaan11",
    //         "saran",
    //         "created_at",
    //         "updated_at"
    //     ];
    // }

    // public function map($row): array
    // {
    //     $fields = [
    //         $row->id,
    //         $row->no_hp,
    //         $row->umur,
    //         $row->jk,
    //         $row->pendidikan,
    //         $row->pekerjaan,
    //         $row->penjamin,
    //         $row->unit,
    //         $row->pertanyaan1,
    //         $row->pertanyaan2,
    //         $row->pertanyaan3,
    //         $row->pertanyaan4,
    //         $row->pertanyaan5,
    //         $row->pertanyaan6,
    //         $row->pertanyaan7,
    //         $row->pertanyaan8,
    //         $row->pertanyaan9,
    //         $row->pertanyaan10,
    //         $row->pertanyaan11,
    //         $row->saran,
    //         $row->created_at,
    //         $row->updated_at
    //     ];
    //     return $fields;
    // }

    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('survei.export_datakepuasan', [
            'dataKepuasan' => $this->data
        ]);
    }
}
