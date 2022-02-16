<?php

namespace App\Http\Controllers\Api;


use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\TranslatableString;
use App\Models\Woman;
use App\Models\WomanList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ToolsController
{
    public function translation_duplicates_remove(Request $request)
    {

        $duplicates = DB::select( DB::raw("SELECT a.* 
                        FROM translatable_strings a 
                        JOIN (SELECT `key`, COUNT(*) FROM translatable_strings GROUP BY `key` HAVING count(*) > 1 ) b 
                            ON a.`key` = b.`key` ORDER BY a.`key`"), array(
            //'somevariable' => $someVariable, // :somevariable
        ));

        $duplicates_ids = [];
        for($i = 0; $i < count($duplicates); $i++){
            if(isset($duplicates[$i+1])){
                if($duplicates[$i]->key === $duplicates[$i+1]->key){
                    $duplicates_ids[] = $duplicates[$i+1]->id;
                }
            }
        }
        //dd($duplicates_ids);

        TranslatableString::query()
            ->whereIn('id', $duplicates_ids)
            ->delete();
    }

    public function remove_optics(Request $request){
        $base_path = public_path();

        $cat1 = ProductCategory::query()
            ->where('id', '=', 264)
            ->with('children')
            ->get()
        ;
        //dd($cats1);

        foreach ($cat1[0]->children as $cat2){

            $products = $cat2->products;
            //dd($products);
            foreach ($products as $product){
                $product_path_part = split_folders_by_id($product->id, true).'/prod_'.$product->id;
                $product_orig_path = $base_path.'/uploads/products/'.$product_path_part.'/';
                $product_optimized_path = $base_path.'/optimized/uploads/products/'.$product_path_part.'/';
                //dd($product->id, $product_orig_path, $product_optimized_path);
                delete_folder($product_orig_path);
                delete_folder($product_optimized_path);
                $product->delete();
            }
        }
    }

    public function generate_sitemap(){
        generate_sitemap();
    }
}