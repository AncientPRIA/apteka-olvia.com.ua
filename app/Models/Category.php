<?php
/**
 * Created by PhpStorm.
 * User: Ancient
 * Date: 2019/08/07
 * Time: 15:58
 */

namespace App\Models;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;
use Kalnoy\Nestedset\NodeTrait;


class Category extends Model
{
    use Translatable,
        NodeTrait; // https://github.com/lazychaser/laravel-nestedset;

    protected $translatable = ['slug', 'name', 'meta_title', 'meta_description', 'meta_h1'];

    protected $table = 'categories';

    //protected $fillable = ['slug', 'name','image_cat'];

    public function parentId()
    {
        return $this->belongsTo(self::class);
    }
    public function parent(){
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
    public function children(){
        return $this->hasMany(self::class, 'parent_id', 'id');
    }


    // [NodeTrait] Run this if _lft and _rgt not filled
    public function node_trait_fill_table(){
        self::fixTree();
    }

    // Get path from $this category to its root
    public function get_path($locale = null){
        if($locale === null){
            $locale = \App::getLocale();
        }
        $path = '';
        $recursive = function($category) use (&$path, &$locale, &$recursive){
            //$category = ProductCategory::query()->first()
            $path = $category->getTranslatedAttribute('slug', $locale, null).'/'.$path;
            if($category->parent_id !== null){
                $recursive($category->parent);
            }
        };
        $recursive($this);
        return trim($path, '/');
    }

    public function get_breadcrumbs($base_url, $base_breadcrumbs){
        $breadcrumbs = [];
        $breadcrumbs_prepare = [];
        $object = $this;

        do{
            $breadcrumbs_prepare[] = [
                'id' => $object->id,
                'title' => $object->name,
                'slug' => $object->slug,
            ];
        }
        while($object = $object->parent);
        $breadcrumbs_prepare = array_reverse($breadcrumbs_prepare);
        $path = $base_url;
        $count = count($breadcrumbs_prepare)-1;
        foreach ($breadcrumbs_prepare as $key=>$breadcrumb){
            $path = $path.'/'.$breadcrumb['slug'];
            $breadcrumbs[$key]['title'] = $breadcrumb['title'];
            $breadcrumbs[$key]['href'] = $path;
        }
        $breadcrumbs = array_merge($base_breadcrumbs, $breadcrumbs);
        return $breadcrumbs;
    }


    public function posts()
    {
        return $this->hasMany(Post::class)
            ->published();
        //->orderBy('id', 'DESC');
    }





    public function get_all_categories_with_counts(){

        $builder = Product::query(); // get builder

        //$table_products = (new Product())->getTable();
        //$table_categories = $this->getTable();

        $result = Category::withCount(['products' => function ($query){
            $query->where('new', '=', '1');
        }])
            ->orderBy('order', 'ASC')
            ->get();
        /*
        $result = Category::query()
                    ->orderBy('order', 'ASC')
                    ->get();
        */


        /**/
 /*       $builder = Product::query(); // get builder

        $table_products = (new Product())->getTable();
//        $table_categories = $this->getTable();

        $result = Category::withCount('products')->get();

        $new_product_cat_ids = \DB::select('select category_id from '.$table_products.' where new = 1');
        $array_cat_new_roduct = array();

        for($i=0;$i<count($new_product_cat_ids);$i++){
            if(!in_array($new_product_cat_ids[$i],$array_cat_new_roduct)){
                array_push($array_cat_new_roduct, $new_product_cat_ids[$i]->category_id);
            }
        }

        for($i=0;$i<count($result);$i++){

            if(in_array($result[$i]->id,$array_cat_new_roduct)){
                $result[$i]["new_product"] = 1;
            } else{
                $result[$i]["new_product"] = 0;
            }

        }
        //dd($result);*/
        /**/


        return $result;

    }

    public function get_category_by_slug($category_slug){

        $table_categories = $this->getTable();

        $result = Category::query()
            ->where($table_categories.'.slug', '=', $category_slug)
            ->first();

        /*
        $result = $builder
            ->where('category_id', '=', $category_id)
            ->join($table_categories, $table_products.'.category_id', '=', $table_categories.'.id')
            ->get();
        */
        return $result;
    }

    public function get_categories_by_ids(array $ids){
        $table_categories = $this->getTable();

        $result = Category::query()
            ->whereIn($table_categories.'.id', $ids);

        return $result;
    }

    // Find category by slug path
    static function scopeFind_by_path(\Illuminate\Database\Eloquent\Builder $query, $segments){
        $segment = array_pop($segments);
        $segments_count = count($segments);
        if($segments_count > 0){
            return $query->where('slug', $segment)->whereHas('parent', self::find_by_path_closure($segments));
        }else{
            return $query->where('slug', $segment)->where('parent_id', '=', null);
        }
    }
    static function find_by_path_closure($segments){
        return function (\Illuminate\Database\Eloquent\Builder $query) use ($segments) {
            $segment = array_pop($segments);
            $segments_count = count($segments);
            if ($segments_count > 0) {
                return $query->where('slug', $segment)->whereHas('parent', self::find_by_path_closure($segments));
            } else {
                return $query->where('slug', $segment)->where('parent_id', '=', null);
            }
        };
    }
    // Find category by slug path END

}