<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UniImport implements ToCollection
{
    // Use example
    // $rows = Excel::toArray(new UniImport(), $product_list["filepath"]);

    public function collection(Collection $rows)
    {
        return $rows;
    }
}