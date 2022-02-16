<?php

namespace App\Helpers;

class Microdata
{
    //public $stored = '';

    public $type_website = null;
    public $type_organization = null;
    public $type_store = null;
    public $type_product = null;
    public $type_article = null;

    public function type_website($url, $name, $search_url_with_search_term_string){
        $data = [
            "@context"=> "http://schema.org",
            "@type"=> "WebSite",
            "url"=>$url,
            "name" => $name,
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => [
                    "@type" => "EntryPoint",
                    "urlTemplate" => $search_url_with_search_term_string // http://dev.workelite.pl/search?s={search_term_string}&loc=
                ],
                "query-input" => [
                    "@type" => "PropertyValueSpecification",
                    "valueRequired" => "http://schema.org/True",
                    "valueName" => "search_term_string",
                ]
            ]
        ];

        $this->type_website = $data;
    }

    public function type_organization($url, $name, $logo = '', $same_as_array = []){
        $data = [
            "@context"=> "http://schema.org",
            "@type"=> "Organization",
            "url"=>$url,
            "name"=> $name,
        ];
        if(count($same_as_array) > 0){
            $data["sameAs"] = $same_as_array;
        }
        if($logo !== ''){
            $data["logo"] = $logo;
        }

        $this->type_organization = $data;
    }

    /*
     *      $open_daytime   Mo, Tu, We, Th, Fr, Sa, Su. 00:00-23:59
     *                      Mo-Su 09:00-19:30
     * */
    public function type_store($name, $description, $image, $open_daytime, $phone, $address){
        $data = [
            "@context"=> "http://schema.org",
            "@type"=> "Store",
            "name"=> $name,
            "description"=> strip_tags($description),
            "image" => $image,
            "openingHours"=> $open_daytime,
            "telephone"=> $phone,
            "address" => $address,
        ];

        $this->type_store = $data;
    }

    public function type_product($category_name, $name, $description, $image, $url, $price = '', $price_currency = ''){

        $offer_data = [
            "@type" => "Offer",
            'availability' => 'http://schema.org/InStock',
            "url" => $url,
        ];
        if($price !== ''){
            $offer_data['price'] = $price;
            $offer_data['priceValidUntil'] = date('Y-m-d', strtotime('+1 year'));
            $offer_data['priceCurrency'] = $price_currency;
        }

        $data = [
            //"@context"=> "http://schema.org",
            "@type"=> "Product",
            "category" => $category_name,
            "name"=> $name,
            "description"=> strip_tags($description),
            "image" => $image,
            "url" => $url,
            "offers" => $offer_data,
        ];

        $this->type_product[] = $data;
    }

    public function type_article($title, $url, $published_date = "2020-01-01", $modified_date = "2020-01-01", $body, $category_name, $image_relative_url, $publisher_name, $publisher_logo_url, $author_name){
        $body = strip_tags($body);
        $image_relative_url = asset('uploads/'.$image_relative_url);
        $data = [
            "@context"=> "http://schema.org",
            "@type"=> "Article",
            "headline" => $title,
            "url" => $url,
            "identifier" => $url,
            "datePublished" => $published_date,
            "dateModified" => $modified_date,
            "articleBody" => $body,
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => $url,
            ],
            "about" => [
                "@type" => "Thing",
                "name" => $category_name
            ],
            "image" => [
                "@type" => "ImageObject",
                "image" => $image_relative_url,
                "url" => $image_relative_url,
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => $publisher_name,
                "logo" => [
                    "@type" => "ImageObject",
                    "image" => $publisher_logo_url,
                    "url" => $publisher_logo_url,
                ]

            ],
        ];
        if($author_name !== ''){
            $data['author'] = [
                "@type" => "Person",
                "name" => $publisher_name,
            ];
        }

        $this->type_article = $data;
    }

    public function generate(array $types){
        $data_array = array();
        $data = '<script type="application/ld+json">';

        foreach ($types as $type){

            $var_name = 'type_'.$type;
            if(isset($this->$var_name) AND $this->$var_name !== null){

                if($type === 'product'){

                    // Multiple products (Category?)
                    if(count($this->$var_name) > 1){

                        // Multiple Products (Itemlist)
                        /*
                        for ($i = 0; $i < count($this->$var_name); $i++){
                            //$this->$var_name[$i]['position'] = $i+1;
                            //$this->$var_name[$i]['@context'] = "http://schema.org";
                        }

                        $data_of_type = [
                            "@context" => "http://schema.org",
                            "@type"=> "ItemList",
                            "numberOfItems"=> count($this->$var_name),
                            "itemListElement"=> $this->$var_name,
                        ];
                        */
                        //$data_of_type = json_encode($data_of_type);

                        // Multiple Product (Plain)

                        foreach ($this->$var_name as $item){
                            $item['@context'] = "http://schema.org";
                            $data_array[] = $item;
                        }

                    }else
                    // Single Product
                    {
                        $item = $this->$var_name[0];
                        $item['@context'] = "http://schema.org";
                        $data_array[] = $item;
                    }

                }else{
                    $data_array[] = $this->$var_name;
                }
            }
        }

        $data = '<script type="application/ld+json">';
        $data .= json_encode($data_array);
        $data .= '</script>';

        return $data;
    }
}