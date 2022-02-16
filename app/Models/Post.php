<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class Post extends Model
{
    use Translatable,
        Resizable;

    protected $translatable = ['title', 'excerpt', 'body', 'slug', 'meta_title', 'meta_description', 'meta_keywords', 'meta_h1'];

    const PUBLISHED = 'PUBLISHED';
    const FEATURED = 1;

    protected $perPage = 8;

    protected $guarded = [];

    public $no_image = "no-image.png";

    public function save(array $options = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->author_id && Auth::user()) {
            $this->author_id = Auth::user()->getKey();
        }

        parent::save();
    }

    public function authorId()
    {
        return $this->belongsTo(Voyager::modelClass('User'), 'author_id', 'id');
    }
    public function categoryId()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Scope a query to only published scopes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished(Builder $query)
    {
        return $query->where('status', '=', static::PUBLISHED);
    }
    // Scope - Featured
    public function scopeFeatured(Builder $query)
    {
        return $query->where('featured', '=', static::FEATURED);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Get path from $this product to its root category
    public function get_path($locale = null){
        if($locale === null){
            $locale = \App::getLocale();
        }
        $path = $this->getTranslatedAttribute('slug', $locale, null);
        $recursive = function($category) use (&$path, &$locale, &$recursive){
            //$category = ProductCategory::query()->first()
            $path = $category->getTranslatedAttribute('slug', $locale, null).'/'.$path;
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

    // MACRO
    // get posts from $category_id and its descendants
    public static function macro_get_posts($category_id, $page = null){
        if(isset($category_id) && $category_id !== null && $category_id !== 0){
            $categories_ids = Category::descendantsAndSelf($category_id)->pluck('id');
        }

        $products = Post::query();
        if(isset($categories_ids) && count($categories_ids) > 0 ){
            $products = $products->whereIn('category_id', $categories_ids);
        }
        $products = $products->published()
            ->orderBy('created_at')
            //->offset(0)
            //->limit(9)
            ->with("category")
            ->paginate(null, $columns = ['*'], $pageName = 'page', $page);
        return $products;
    }


}
