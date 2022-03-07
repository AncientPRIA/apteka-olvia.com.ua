<?php

namespace App\Models;

use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use TCG\Voyager\Traits\Translatable;
use Kalnoy\Nestedset\NodeTrait;


class ProductCategory extends Model
{
    // ###################### INIT ######################
    use Translatable,
        NodeTrait; // https://github.com/lazychaser/laravel-nestedset

    protected $translatable = ['slug', 'name', 'meta_title', 'meta_description', 'meta_h1'];

    const FEATURED = 1;

    //protected $table = 'categories';

    //protected $fillable = ['slug', 'name','image'];

    // #################### INIT END ####################

    // #################### HELP ########################
    // get self, descendants in array (flat)
    // $categories = ProductCategory::descendantsAndSelf(1)
    // ->pluck('id');                    // retrieve ids (for whereIn)
    // ->toTree() or to ->toFlatTree()   // make recursive tree or flat tree
    // #################### HELP END ####################

    public function save(array $options = [])
    {
        if (!$this->slug) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }

        parent::save();
    }

    // Scope - Featured
    public function scopeFeatured($query)
    {
        return $query->where('featured', '=', static::FEATURED);
    }

    // [NodeTrait] Run this if _lft and _rgt not filled
    public function node_trait_fill_table(){
        self::fixTree();
    }

    public function parentId()
    {
        return $this->belongsTo(self::class);
    }


    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id')
            ->published();
    }

    // Not works
    public function products_recursive()
    {
        return $this->hasManyThrough(Product::class, ProductCategory::class, 'parent_id', 'category_id', 'id');
    }

    public function parent(){
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
    public function children(){
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function get_root(){
        $recursive = function ($category) use (&$recursive){
            if($category->parent_id === null){
                return $category;
            }else{
                return $recursive($category->parent);
            }
        };
        return $recursive($this);
    }

    // Get path from $this category to its root
    public function get_path(){
        //dd($this->id);
        $path = '';
        $recursive = function($category) use (&$path, &$recursive){
            //$category = ProductCategory::query()->first()
            $path = $category->slug.'/'.$path;
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

    public function scopeNot_empty(\Illuminate\Database\Eloquent\Builder $query){

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

/*
"active" => true,
"title"=>"tile",
"link"=>"#",
    //"icon"=>"img/svg/user.svg",
"name"=>"32",
"submenu"=>[
*/
    // not finished
    public static function menu_tree($id = null, $active_path = null){
        $menu = [];
        $categories = ProductCategory::query();
        if($id !== null){
            $categories = $categories->where('id', $id);
        }
        $categories = $categories->get();

        $segments = explode('/', trim($active_path, '/'));

        $recursive = function($category) use (&$recursive, &$menu, &$segments) {
            if($category !== null){
                $menu_segment = $category->slug;


                $menu_array = [

                ];
            }else{
                return null;
            }
        };


        foreach ($categories as $category){
            $path = $category->slug;

            $menu[] = [
                //"active" => true,
                "title"=>$category->name,
                "link"=>route('products').'/'.$path,
                //"icon"=>"img/svg/user.svg",
                "name"=>$category->id,
                //"submenu"=>[]

            ];
            $recursive($category);
        }


    }

    // Works
    public static function menu_tree_simple($id = null, $active_id = null){
        $menu = [];
        $categories = ProductCategory::query();
        if($id !== null){
            $categories = $categories->where('id', $id);
        }else{
            $categories = $categories->where('parent_id', null);
        }
        $categories = $categories->get();

        $recursive = function($category, $path) use (&$recursive, &$active_id) {
            if($category !== null){
                $menu_segment = $category->slug;
                $path = $path.$menu_segment.'/';
                $menu_array = [
                    //"active" => true,
                    "title" => $category->name,
                    "link" => route('products').'/'.$path,
                    //"icon"=>"img/svg/user.svg",
                    "name" => $category->id,
                    //"submenu"=>[]
                ];
                if($category->id === $active_id){
                    $menu_array['is_current'] = true;
                }
                $category_children = $category->children;
                if(count($category_children) > 0){
                    foreach ($category_children as $category_child) {
                        $menu_sub[] = $recursive($category_child, $path);
                    }
                    $menu_array['submenu'] = $menu_sub;

                }
                return $menu_array;

            }else{
                return null;
            }
        };

        foreach ($categories as $category){
            $menu[] = $recursive($category, '');
        }

        return $menu;


    }



}
