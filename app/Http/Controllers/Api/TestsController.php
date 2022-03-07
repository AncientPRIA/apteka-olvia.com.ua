<?php

namespace App\Http\Controllers\Api;


use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\TranslatableString;
use Illuminate\Support\Facades\Request;

class TestsController
{
    public function filled_categories(){
        $categories = ProductCategory::query()
            ->whereHas("products")
            ->get()
        ;
        dd($categories);
    }


}