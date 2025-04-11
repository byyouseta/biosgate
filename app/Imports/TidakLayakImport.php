<?php

namespace App\Imports;

use App\KlaimPending;
use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TidakLayakImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        return new KlaimPending([
            'no_sep' => $row['no_sep'],
            'alasan' => $row['alasan']
        ]);
    }
}
