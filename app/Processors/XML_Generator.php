<?php
/**
 * Created by PhpStorm.
 * User: Ancient
 * Date: 2019/09/24
 * Time: 14:21
 */

namespace App\Processors;


class XML_Generator{
    protected $xml = null;

    private function create_xml($root_element = "root"){
        $header = '<?xml version="1.0" encoding="UTF-8"?><'.$root_element.'></'.$root_element.'>';
        $this->xml = new \SimpleXMLElement($header);
    }


    /*------------ yandex realty xml START ------------*/
    public function init_yandex_realty_xml(){
        $this->create_xml('realty-feed');
        $this->xml->addAttribute('xmlns', 'http://webmaster.yandex.ru/schemas/feed/realty/2010-06');
        $this->xml->addChild('generation-date', str_replace('+00:00', '+03:00', date('c')));
    }
    public function add_yandex_realty_offer($offer_arr){

        $xml_offer = $this->xml->addChild('offer');
        $xml_offer->addAttribute('internal-id', $offer_arr['id']);
        $xml_offer->addChild('type', $offer_arr['type']);
        $xml_offer->addChild('property-type', $offer_arr['property_type']);
        $xml_offer->addChild('category', $offer_arr['category']);
        $xml_offer->addChild('creation-date', $offer_arr['creation_date']);
        $xml_offer_location = $xml_offer->addChild('location');
        $xml_offer_location->addChild('country', $offer_arr['country']);
        $xml_offer_location->addChild('locality-name', $offer_arr['locality_name']);
        $xml_offer_location->addChild('address', $offer_arr['address']);
        if($offer_arr['apartment_number'] !== '' || $offer_arr['apartment_number'] !== null){
            $xml_offer_location->addChild('apartment', $offer_arr['apartment_number']);
        }
        $xml_offer_sales_agents = $xml_offer->addChild('sales-agent');
        $xml_offer_sales_agents->addChild('category', 'agency');
        foreach ($offer_arr['agency_phone'] as $agency_phone){
            $xml_offer_sales_agents->addChild('phone', $agency_phone);
        }

        $xml_offer_price = $xml_offer->addChild('price');
        $xml_offer_price->addChild('value', $offer_arr['price']);
        $xml_offer_price->addChild('currency', $offer_arr['currency']);
        $xml_offer_area = $xml_offer->addChild('area');
        $xml_offer_area->addChild('value', $offer_arr['area']);
        $xml_offer_area->addChild('unit', $offer_arr['area_unit']);
        $xml_offer->addChild('rooms', $offer_arr['rooms']);
        $xml_offer->addChild('rooms-offered', $offer_arr['rooms']);
        $xml_offer->addChild('floor', $offer_arr['floor']);

    }
    /*------------ yandex realty xml END ------------*/

    /*------------ sitemap xml START ------------*/
    public function init_sitemap_xml(){
        $this->create_xml('urlset');
        $this->xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $this->xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->xml->addAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.w3.org/1999/xhtml http://www.w3.org/2002/08/xhtml/xhtml1-strict.xsd');
        $this->xml->addAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
    }
    /*
     * $change_freq = 'daily' || 'monthly'
     *
     *
     * */
    public function add_sitemap_url($url, $mod_date, $change_freq = 'monthly', $priority = 1.0){
        $xml_url = $this->xml->addChild('url');
        $xml_url->addChild('loc', $url);
        $xml_url->addChild('lastmod', $mod_date);
        $xml_url->addChild('changefreq', $change_freq);
        $xml_url->addChild('priority', $priority);
    }

    public function add_multi_sitemap($url, $mod_date){
//        $this->create_xml('sitemapindex');
//
//        $xml_url = $this->xml->addChild('sitemap');
//        $xml_url->addChild('loc', $url);
//        $xml_url->addChild('lastmod', $mod_date);

        $this->create_xml('sitemapindex');
        foreach ($url as $url_item){
            $xml_url = $this->xml->addChild('sitemap');
            $xml_url->addChild('loc', $url_item);
            $xml_url->addChild('lastmod', date('Y-m-d'));
        }
    }
    /*------------ sitemap xml END ------------*/

    public function print_xml(){
        // Print
        Header('Content-type: text/xml');
        print($this->xml->asXML());
    }
    public function get_xml(){
        // Return
        return $this->xml->asXML();
    }
}