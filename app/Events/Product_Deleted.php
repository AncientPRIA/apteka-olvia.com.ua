<?php
/**
 * Created by PhpStorm.
 * User: Ancient
 * Date: 2019/09/24
 * Time: 14:34
 */

namespace App\Events;

use App\Processors\XML_Generator;
use App\Models\Category;

class Product_Deleted
{
    //public $product;

    /**
     * Create a new event instance.
     *
     * @param  App\Models\Product  $product
     * @return void
     */
    public function __construct() // Product $product
    {
        generate_sitemap();
    }

}