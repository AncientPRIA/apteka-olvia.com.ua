<?php


namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SynchronizerController
{

    public function test(){

    }

    public function xls_sync(){
        $product_list["filename"] = "product_list.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        Excel::load($product_list["filepath"], function($excel) {
            $excel->sheet(0, function($sheet){
                $excel_products_arr = $sheet->toArray();
                dd($excel_products_arr);
            });
        });

    }

}