<?php

namespace App\Models;

use App\Events\Product_Created;
use App\Events\Product_Deleted;
use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;


class Product extends Model
{
    use Translatable,
        \Staudenmeir\EloquentHasManyDeep\HasRelationships; // https://github.com/staudenmeir/eloquent-has-many-deep

    protected $translatable = [
        'title', 'slug'
    ];
    protected $casts = [
        //'image' => 'array',
    ];
    protected $perPage = 12;

    public $timestamps = true;
    const PUBLISHED = 'PUBLISHED';
    const SYNCED_CATALOG = 'SYNCED_CATALOG';

    const FEATURED = 1;

    const PICTURE_BASE_KEY = "product";

    public $no_image = "product_image_placeholder.png";

    protected $dispatchesEvents = [
        'created' => Product_Created::class,
        'deleted' => Product_Deleted::class,
    ];

    public function save(array $options = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->author_id && Auth::user()) {
            $this->author_id = Auth::user()->getKey();
        }

        if (!$this->slug) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }

        parent::save();
    }

    // Scope - Published
    public function scopePublished(Builder $query)
    {
        return $query;
        //return $query->whereIn('status', [static::PUBLISHED, static::SYNCED_CATALOG]);
    }

    // Scope - Featured
    public function scopeFeatured(Builder $query)
    {
        return $query->where('featured', '=', static::FEATURED);
    }

    public function authorId()
    {
        return $this->belongsTo(Voyager::modelClass('User'), 'author_id', 'id');
    }

    public function categoryId()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
    // List for Select Dropdown
//    public function authorIdList(){
//        return User::where('active', 1)->orderBy('created_at')->get();
//    }


    public function ratings()
    {
        $query = $this->hasMany(ProductRating::class, 'product_id', 'id');
        return $query;
    }

    public function get_user_rating($user_id){
        $query = $this->hasOne(ProductRating::class, 'product_id', 'id')
            ->where('user_id', '=', $user_id)
            ->where('product_id', '=', $this->id)
            ->first();
        return $query;
    }

    public function get_average_rating(){
        $query = $this->hasOne(ProductRating::class, 'product_id', 'id')
            ->where('product_id', '=', $this->id)
            ->avg('rating');
        return $query;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'id')
            ->published();
        //->orderBy('id', 'DESC');
    }


    /*
    public function getCategoryAttribute(){
        return $this->categories()->first();
    }
    */

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    /**
     * Return alternative User Roles.
     */

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'products_product_categories_pivot');
    }

    /**
     * Return all Categories, merging the default and alternative.
     */
    public function categories_all()
    {
        if (!$this->relationLoaded('category')) {
            $this->load('category');
        }

        if (!$this->relationLoaded('categories')) {
            $this->load('categories');
        }

        return collect([$this->category])->merge($this->categories);
    }

    public function active_substances()
    {
        return $this->belongsToMany(ActiveSubstance::class, 'products_active_substances_pivot');
    }

    public function availability()
    {
        return $this->hasOne(ProductAvailability::class, 'product_id', 'id');
    }

    // Get products related to product in order
    public function related_by_order(){

/*
This will filter Player based on a related table

Player::whereHas("roleplay", function($q){
   $q->where("column_name","=","value");
})->get();
* */


        $orders = OrdersItem::query()
            ->select('order_id')
            ->where('product_id', $this->id)
            ->get()
            ->toArray()
        ;

        $product_ids = OrdersItem::query()
            ->whereIn('order_id', $orders)
            ->where('product_id', '!=', $this->id)
            ->get('product_id')
            ->toArray()
        ;

        $products = Product::query()
            ->whereIn('id', $product_ids)
            ->get()
        ;

        return $products;
    }




    // Get path from $this product to its root category
    public function get_path(){
        //dd($this->id);
        $path = $this->slug;
        $recursive = function($category) use (&$path, &$recursive){
            //$category = ProductCategory::query()->first()
            $path = $category->slug.'/'.$path;
            if($category->parent_id !== null){
                $recursive($category->parent);
            }
        };
        $category = $this->category;
        if($category !== null){
            $recursive($category);
        }
        return trim($path, '/');
    }

    public function get_breadcrumbs($base_url, $base_breadcrumbs){
        $breadcrumbs = [];
        $object = $this;

        do{
            $breadcrumbs[] = [
                'id' => $object->id,
                'title' => $object->name,
                'slug' => $object->slug,
            ];
        }
        while($object = $object->parent);
        $breadcrumbs = array_reverse($breadcrumbs);
        $path = $base_url;
        $count = count($breadcrumbs)-1;
        foreach ($breadcrumbs as $key=>$breadcrumb){
            $path = $path.'/'.$breadcrumb['slug'];
            if($key < $count){
                $breadcrumbs[$key]['href'] = $path;
            }
        }
        $breadcrumbs = array_merge($base_breadcrumbs, $breadcrumbs);

        return $breadcrumbs;
    }


    // MACRO
    // get products from $category_id and its descendants
    public static function macro_get_products($category_id, $page = null, $sorting = 'created_at|desc'){
        $sorting = explode('|', $sorting);
        if(isset($category_id) && $category_id !== null && $category_id !== 0){
            $categories_ids = ProductCategory::descendantsAndSelf($category_id)->pluck('id');
        }

        $products = Product::query();
        if(isset($categories_ids) && count($categories_ids) > 0 ){
            $products = $products
                ->whereHas('categories', function ($query) use ($categories_ids){
                    $query->whereIn('product_category_id', $categories_ids);
                })
                ->orWhereIn('category_id', $categories_ids);
            //$products = $products->whereIn('category_id', $categories_ids);
        }
        $products = $products->published()
            ->orderBy($sorting[0], $sorting[1])
            //->offset(0)
            //->limit(9)
            ->with("categories")
            ->paginate(null, $columns = ['*'], $pageName = 'page', $page);
        return $products;
    }


    /* ========= IMAGES ========= */

    // Make Picture ID of $type
    public function picture_id($relative, $type){
        return self::PICTURE_BASE_KEY . "_" . $type . "_" . $relative;
    }

    // Get images by type
    public function get_images($type){
        switch ($type){
            case "card":
            case "thumb":
                $images = $this->get_images_thumb();
                break;
            case "full":
                $images = $this->get_images_full();
                break;
            default:
                $images = [];
        }
        return $images;
    }

    // Get main images
    public function get_images_full(){
        $images = json_decode($this->image, true) ?? $this->image;
        if(empty($images)){
            $images = json_decode($this->image_thumb, true) ?? $this->image_thumb;
        }
        if(empty($images)){
            $images = $this->no_image ?? '';
        }
        if(!is_array($images)){
            $images = [$images];
        }
        return $images;
    }

    // Get card images (thumb)
    public function get_images_thumb(){
        $images = json_decode($this->image_thumb) ?? $this->image_thumb;
        if(empty($images)){
            $images = json_decode($this->image) ?? $this->image;
        }
        if(empty($images)){
            $images = $this->no_image ?? '';
        }
        if(!is_array($images)){
            $images = [$images];
        }
        return $images;
    }

    // Generate image of $type
    public function picture($uploads_relative, $type){
        switch ($type){
            case "card":
            case "thumb":
                $picture = $this->picture_card($uploads_relative);
                break;
            case "full":
                $picture = $this->picture_full($uploads_relative);
                break;
            case "search":
                $picture = $this->picture_search($uploads_relative);
                break;
            default:
                $picture = "";
        }
        return $picture;
    }

    // Generate image for single page
    public function picture_full($uploads_relative){
        $type = "full";

        $sizes = array(
            '1' => ['width' => 376, 'relative_path' => 'uploads/'.$uploads_relative, 'q'=> 90, 'watermark' => config('image.watermark.product')],
            'id' => $this->picture_id($uploads_relative, $type),
            'get_dimensions' => true
        );
        $mappings = array(
            '>320' => '1',
            'default' => '1'
        );
        $sizes = \Img::img($sizes);
        $picture = \Img::picture_compose($sizes, $mappings, [
            'is_hidden' => false,
            'classes' => '',
            'alt' => '',
            'is_lazy' => true,
            'fullscreen' => true,
            'placeholder' => [
                'plain' => asset('uploads/product_image_placeholder.png'),
            ]
        ]);
        return $picture;
    }

    // Generate image for card
    public function picture_card($uploads_relative){
        $type = "card";

        $sizes = array(
            '1' => ['width' => 320, 'relative_path' => 'uploads/'.$uploads_relative, 'q'=> 60, 'watermark' => config('image.watermark.product')],
            '2' => ['width' => 540, 'relative_path' => 'uploads/'.$uploads_relative, 'q'=> 60, 'watermark' => config('image.watermark.product')],
            'id' => $this->picture_id($uploads_relative, $type)
        );
        $mappings = array(
            '>540' => '2',
            '>320' => '1',
            'default' => '1'
        );
        $sizes = \Img::img($sizes);
        $picture = \Img::picture_compose($sizes, $mappings, true, '', '', true);

        return $picture;
    }

    // Generate image for card
    public function picture_search($uploads_relative){
        $type = "search";

        $sizes = array(
            '1' => ['width' => 70, 'relative_path' => 'uploads/'.$uploads_relative, 'q'=> 60, 'watermark' => config('image.watermark.product')],
            'id' => $this->picture_id($uploads_relative, $type)
        );
        $mappings = array(
            '>320' => '1',
            'default' => '1'
        );
        $sizes = \Img::img($sizes);
        $picture = \Img::picture_compose($sizes, $mappings, false, 'search-result-category__item-img', '', false);

        return $picture;
    }

}










