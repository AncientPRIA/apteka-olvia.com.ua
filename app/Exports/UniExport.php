<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class UniExport implements FromCollection
{
    private $data;

    public function __construct($array, $header = null)
    {
        $this->data = $array;
        if($header !== null){
            array_unshift($this->data, $header);
        }
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data);
    }
}
