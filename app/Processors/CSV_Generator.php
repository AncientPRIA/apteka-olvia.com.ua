<?php
// Header templates in the end of file

namespace App\Processors;

class CSV_Generator{

    private $file_handler;

    function set_file($full_path, $overwrite = false){
        if($overwrite){
            if(file_exists($full_path)){
                unlink($full_path);
            }
            $handler = fopen($full_path, 'a');

        }else{
            $handler = fopen($full_path, 'a');
        }

        if($handler){
            $this->file_handler = $handler;
            return true;
        }else{
            return false;
        }
    }

    function add_row($values){
        fputcsv($this->file_handler, $values);
    }

    function get_csv($csv_file){
        //$csv = array_map('str_getcsv', file($csv_file));
        //dd($csv);
        while ($row = fgetcsv($this->file_handler)) {
            $csv[] = $row;
        }
        return $csv;
    }


    function generate_csv_for_facebook($exit = true){
        ini_set('display_errors', 1);
        global $custom_vars;
        include $custom_vars['php_dir'].'/libraries/csv/CSV_Generator.php';

        $csv_path = $custom_vars['content_dir'].'/data/csv/properties_facebook.csv';

        $csv_gen = new CSV_Generator();
        if($csv_gen->set_file($csv_path, true)){

            $header = [
                'id',                           # Required | Enter a unique ID for the item, such as a SKU. If there are multiple instances of the same ID, these will be ignored. | Max characters: 100
                'title',                        # Required | The title of the item. | Max character limit: 100
                'description',                  # Required | A short description describing the item. | Max character limit: 5000
                'availability',                 # Required | The current availability of the item in your store. | Supported values: in stock, available for order, preorder, out of stock, discontinued
                'condition',                    # Required | The current condition of the item in your store. | Supported values: new, refurbished, used
                'price',                        # Required | The cost and currency of the item. The price is a number followed by the currency code (ISO 4217 standards)
                'link',                         # Required | The URL of the website where you can buy the item
                'image_link',                   # Required | The URL for the image used in your ad. For square (1:1) aspect ratios in the carousel ad format, your image should be 600x600. For single image ads, your image should be at least 1200x630
                'brand',                        # Required | You can use unique manufacturer part number (MPN), Global Trade Item Number (GTIN), or brand name for your product. You only need to use one of these values for this column (not all of them). | Supported values for GTIN: UPC, EAN, JAN and ISBN | Max character limit: 70
            ];
            $csv_gen->add_row($header);

            $query = new WP_Query([
                'post_status' => 'publish',
                'post_type' => 'cf47rs_property',
                'posts_per_page' => '-1',
                'orderby' => 'date',
                'order' => 'ASC',
            ]);

            foreach ($query->posts as $property){

                $price = get_field('cf47rs_price', $property->ID);
                $currency = get_field('cf47rs_currency', $property->ID);
                $currency = $custom_vars['data']['currency'][$currency];
                $price = $price.' '.$currency;
                $link = get_permalink($property->ID);
                $rooms = get_field('cf47rs_rooms', $property->ID);
                $thumb_id = get_post_thumbnail_id($property->ID);
                if($thumb_id === ''){
                    $image_link = $custom_vars['uploads_url'].'/not-img-obj.jpg';
                }else{
                    $img_obj = wp_get_attachment_image_src($thumb_id, 'full');
                    $image_link = $img_obj[0];
                }

                if($property->post_content === ''){

                    $description = 'Продаётся ';

                    $street = wp_get_post_terms($property->ID, 'cf47rs_property_location');
                    $district = get_term($street[0]->parent, 'cf47rs_property_location')->name;

                    switch ($rooms){
                        case 1:
                            $description .= 'однокомнатная квартира';
                            break;
                        case 2:
                            $description .= 'двухкомнатная квартира';
                            break;
                        case 3:
                            $description .= 'трёхкомнатная квартира';
                            break;
                        case 4:
                            $description .= 'четырёхкомнатная квартира';
                            break;
                        case 5:
                            $description .= 'пятикомнатная квартира';
                            break;
                    }
                    $description .= " в ".str_replace('кий', 'ком', $district)." районе. \nАгентство недвижимасти Pria Agency";

                }else{
                    $description = $property->post_content;
                }

                $row = [
                    $property->ID,
                    $property->post_title,
                    $description,
                    'in stock',
                    'new',
                    $price,
                    $link,
                    $image_link,
                    'Pria Agency',
                ];

                $csv_gen->add_row($row);

            }


        }

        if($exit){exit();}
    }

}

// FACEBOOK (ANY) FEED CSV TEMPLATE
/*
$header = [
'id',                           # Required | Enter a unique ID for the item, such as a SKU. If there are multiple instances of the same ID, these will be ignored. | Max characters: 100
'title',                        # Required | The title of the item. | Max character limit: 100
'description',                  # Required | A short description describing the item. | Max character limit: 5000
'availability',                 # Required | The current availability of the item in your store. | Supported values: in stock, available for order, preorder, out of stock, discontinued
'condition',                    # Required | The current condition of the item in your store. | Supported values: new, refurbished, used
'price',                        # Required | The cost and currency of the item. The price is a number followed by the currency code (ISO 4217 standards)
'link',                         # Required | The URL of the website where you can buy the item
'image_link',                   # Required | The URL for the image used in your ad. For square (1:1) aspect ratios in the carousel ad format, your image should be 600x600. For single image ads, your image should be at least 1200x630
'brand',                        # Required | You can use unique manufacturer part number (MPN), Global Trade Item Number (GTIN), or brand name for your product. You only need to use one of these values for this column (not all of them). | Supported values for GTIN: UPC, EAN, JAN and ISBN | Max character limit: 70
'additional_image_link',        # Optional | Additional image URLs for the item. You can include up to 10 image URLs. Use "","" to separate each URL. | Max character limit: 2000
'age_group',                    # Optional | The age group for your item. | Supported values: newborn, infant, toddler, kids, adult
'color',                        # Optional | The item color. | Max character limit: 100
'gender',                       # Optional | The item's gender. | Supported values: male, female, unisex"
'item_group_id',                # Optional | Items that are varients of a specific product. Provide the same item_group_id for all items that are varients. For example, a red Polo Shirt is a variant of Polo Shirt. Facebook maps this to the retailer_product_group_id once we get your feed. For dynamic ads, Facebook picks only one item out of the group based on the signal we receive from the pixel or app event
'google_product_category',      # Optional | A preset value from Google's product taxonomy. For example: Apparel & Accessories > Clothing > Dresses | Max character limit: 250
'material',                     # Optional | The material the product is made from, such as cotton, denim or leather. | Max character limit: 200
'pattern',                      # Optional | Pattern or graphic print on a product. | Max character limit: 100
'product_type',                 # Optional | The category the product belongs in. For example: Home & Garden > Kitchen & Dining > Appliances > Refrigerators | Max character limit: 750
'sale_price',                   # Optional | The discounted cose and currency code for the product if it's on sale. The sale price is a number followed by the currency code (ISO 4217 standards). Use ""."" as the decimal for the sale price. The sale price is required if you plan on using an overlay for discounted prices
'sale_price_effective_date',    # Optional | The start and end date and time for your sale, written as: YYYY-MM-DDT0:00-23:59/YYYY-MM-DDT0:00-23:59
'shipping',                     # Optional | The type of shipping for the item, written as: COUNTRY:STATE:SHIPPING_TYPE:PRICE. Use "";"" to separate different regions. For example: US:CA:Ground:9.99 USD,US:NY:Air:15.99 USD
'shipping_weight',              # Optional | The shipping weight of the item. | Supported values: lb, oz, g, kg
'size',                         # Optional | The size of the item. For example: small or XL
'custom_label_0',               # Optional | Additional information about the product you want to include. | Max character limit: 100
'custom_label_1',               # Optional | Additional information about the product you want to include. | Max character limit: 100
'custom_label_2',               # Optional | Additional information about the product you want to include. | Max character limit: 100
'custom_label_3',               # Optional | Additional information about the product you want to include. | Max character limit: 100
'custom_label_4'                # Optional | Additional information about the product you want to include. | Max character limit: 100
];
*/
// FACEBOOK (ANY) FEED CSV TEMPLATE

// FACEBOOK PROPERTY FEED CSV TEMPLATE
/*
$header = [
    'home_listing_id',          # Required | The unique ID for the home listing
    'name',                     # Required | The name of the home listing
    'availability',             # Required | The current availability of the listing. | Supported: for_sale, for_rent, sale_pending, recently_sold, off_market, available_soon
    'address.addr1',            # Required | The street address for the listing
    'address.city',             # Required | The city for the listing
    'address.region',           # Required | The state, county, region or province of the listing
    'address.country',          # Required | The country for the listing
    'address.postal_code',      # Required only if for countries with a postal code system| Postal or zip code for the listing
    'latitude',                 # Required | The latitude of the listing
    'longitude',                # Required | The longitude of the listing
    'neighborhood[0]',          # Required | The neighborhood for the home listing. If you have more than one neighborhood, add additional columns for each type and use JSON-path syntax in each column name to indicate the number of neighborhoods (for example: neighborhood[0]; neighborhood[1]). | Max neighborhoods allowed: 20
    'image[0].url',             # Required | The URL for the image used in your ad. For square (1:1) aspect ratios in the carousel ad format, your image should be 600x600. For single image ads, your image should be at least 1200x630 pixels. If you have more than one image, add additional columns for each type and use JSON-path syntax in each column name to indicate the number of images (for example: image[0].url; image[1].url). | Max items: 20
    'price',                    # Required | The cost and currency of the home listing. The price is a number followed by the currency code (ISO 4217 standards)
    'url',                      # Required | A link to the website where you can view the listing
    'description',              # Optional | A description of the home listing. | Max characters: 5000
    'image[0].tag[0]',          # Optional | A tag appended to the image that shows what's in the image. For example, front door or pool
    'num_beds',                 # Optional | Number of bedrooms
    'num_baths',                # Optional | Number of baths
    'property_type',            # Optional | The type of property | Supported: apartment, condo, house, land, manufactured, other, townhouse
    'listing_type',             # Optional | The type of listing | Supported: for_rent_by_agent, for_rent_by_owner, for_sale_by_agent, for_sale_by_owner, foreclosed, new_construction, new_listing
    'num_units',                # Optional | The number of units available. Use only for apartments or condos available for rental
];
*/
// FACEBOOK PROPERTY FEED CSV TEMPLATE END