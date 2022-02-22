<?php


namespace App\Http\Controllers\Api;

use App\Imports\UniImport;
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

    // See xml data examples at bottom

    // =====================================================
    // =    INIT
    // =====================================================

    public $shops_map = null;
    public $shops_site = null;

    // Goal: [
    //  {xml shop index} => [
    //      "id" => {site shop id}
    //      "object" => {model}
    //  ]
    //]

    // =====================================================
    // =    FUNCTIONAL
    // =====================================================

    public function products_sync(){
        echo "<pre>";

        // Define paths and other vars
        $product_list["filename"] = "product_list.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];

        // Skip indexes (null if none)
        $skip_indexes = [0];

        // Iterate
        foreach ($rows as $row_index => $row){
            // Skips
            if($skip_indexes !== null){
                if(array_search($row_index, $skip_indexes) !== false){
                    continue;
                }
            }

            //
        }

        echo "</pre>";

    }

    public function availability_sync(){
        echo "<pre>";

        // Define paths and other vars
        $product_list["filename"] = "availability.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];


        $this->get_shops_site();

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];

        // Skip indexes (null if none)
        $skip_indexes = [0, 1, 2, 3];

        // Iterate
        foreach ($rows as $row_index => $row){
            // Skips
            if($skip_indexes !== null){
                if(array_search($row_index, $skip_indexes) !== false){
                    continue;
                }
            }

            //
        }

        echo "</pre>";

    }

    public function process_product_row($row){
        $product = [];
        $product["sku"] = $row[0];
        $product["title"] = $row[1];
        $product["release_form"] = $row[2];
        $product["amount_in_package"] = $row[3];
        $product["brand"] = $row[4];
        $product["nomenclature"] = $row[5];


    }

    public function process_availability_row($row){
        $product = [];
        $product["sku"] = $row[1];      // int
        $product["price"] = $row[7];    // float possible!

        // Shop list
        // Need
        for ($i = 8; ; $i++){

        }


    }

    // Set and return site shops sorted by $ids
    public function get_shops_site($ids){
        if($this->shops_site === null){
            $this->shops_site = []; // TODO: Get shops and sort by array
        }

        return $this->shops_site;
    }

    // Set and return shops map
    public function get_shops_map(){
        // xml shop index => site shop model
        if($this->shops_map === null){
            $this->shops_map = [
                8 => 1,
            ];
        }

        return $this->shops_map;
    }

    // =====================================================
    // =    TESTS
    // =====================================================


    public function test_products_list(){
        echo "<pre>";

        // Define paths and other vars
        $product_list["filename"] = "product_list.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];
        dd($rows);

        // Iterate
        foreach ($rows as $row_index => $row){
            if($row_index > 20){
                break;
            }

            var_dump($row);
            echo "\n\n";
        }

        echo "</pre>";
    }

    public function test_availability(){
        echo "<pre>";

        // Define paths and other vars
        $product_list["filename"] = "availability.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];

        // Iterate
        foreach ($rows as $row_index => $row){
            if($row_index > 20){
                break;
            }

            var_dump($row);
            echo "\n\n";
        }

        echo "</pre>";
    }

}

/**
Products list

Header
array(6) {
[0]=>
string(6) "Код"
[1]=>
string(24) "Наименование"
[2]=>
string(25) "Форма выпуска"
[3]=>
string(20) "Кол-во в уп."
[4]=>
string(10) "Бренд"
[5]=>
string(24) "Номенклатура"
}

Row
array(6) {
[0]=>
int(7289)
[1]=>
string(93) " Oral-B насадка для зубной щетки  3D WHITE отбеливающая №2"
[2]=>
string(14) "Насадка"
[3]=>
int(2)
[4]=>
string(6) "Oral B"
[5]=>
string(93) "З/щ Oral-B насадка для з/щ 3D WHITE отбеливающая №2 *7757 ТП-5%"
}

 */

/**
Availability shops

[8]=>
string(51) "Аптека №18 (Шахтостроителей)"
[9]=>
NULL
[10]=>
string(54) "Аптека №1 (Красноармейская, 56)"
[11]=>
NULL
[12]=>
string(49) "Аптека №12 (Островского,70/20)"
[13]=>
NULL
[14]=>
string(52) "Аптека №15 (250-лет Донбасса 5в)"
[15]=>
NULL
[16]=>
string(37) "Аптека №13 (Енакиево)"
[17]=>
NULL
[18]=>
string(40) "Аптека №6 (ул. Пухова,1)"
[19]=>
NULL
[20]=>
string(44) "Аптека №9 (Ферганская, 41)"
[21]=>
NULL
[22]=>
string(50) "Аптека №7 (50-летия СССР, 155-б)"
[23]=>
NULL
[24]=>
string(58) "Аптека №2 (Римского-Корсакова, 2)"
[25]=>
NULL
[26]=>
string(44) "Аптека №16 (Ленинский 47г)"
[27]=>
NULL
[28]=>
string(44) "Аптека №3 (Раздольная, 15)"
[29]=>
NULL
[30]=>
string(42) "Аптека №14 (пл.Победы, 31)"
[31]=>
NULL
[32]=>
string(60) "Аптека №17 (к-л,Железнодорожный,37)"
[33]=>
NULL
[34]=>
string(37) "Аптека №21 (Щетинина)"
[35]=>
NULL
[36]=>
string(37) "Аптека №11 (Горловка)"
[37]=>
NULL
[38]=>
string(43) "Аптека №4 (ул. Артёма, 183)"
[39]=>
NULL
[40]=>
string(67) "Аптека №8 (230-й Стрелковой Дивизии, 9а)"
[41]=>
NULL
[42]=>
string(61) "Аптека №19 (ОсвобождениеДонбасса)"
[43]=>
NULL
[44]=>
string(45) "Аптека №10 (Терешкова, 15б)"
[45]=>
NULL
[46]=>
string(44) "Аптека №5 (Коммунаров, 52)"
[47]=>
NULL
[48]=>
string(75) "Аптека №1 Медизделия (Красноармейская, 56)"
[49]=>
NULL
[50]=>
string(64) "Подразд.ФЛП Тимченко А.Г. Пушкина,7б"

 */