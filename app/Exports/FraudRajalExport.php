<?php

namespace App\Exports;

use App\FraudRajal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\FromView;

class FraudRajalExport implements FromView
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
        return view('vedika.export_rajalfraud', [
            'dataFraud' => $this->data
        ]);
    }
}
