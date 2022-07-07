<?php

namespace App;
require './vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;

class Product
{ 
    public function removeSybol($value){
        
      $numStr = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
      return (float)$numStr;

    }
   
    public function convertToMb($value)
    {
      $res = preg_replace("/[^0-9]/", "",$value);

      if(str_contains($value,'MB'))
      {
         $num = (int)$res;
         return $num;
      }
      else{
      $num = (int)$res * 1000;
      return $num;
      }

    }
  
    public function imgUrl($value) {
      return str_replace("..","",$value);
    }

    public function run(): void
    {
        
      $crawler = ScrapeHelper::fetchDocument('https://www.magpiehq.com/developer-challenge/smartphones');
      $crawler =  $crawler->filter('.product')->each(function (Crawler $node, $i){
           
       return $node;


        });

        

      $products = array();
        
      
      foreach($crawler as $node) {
        $title = $node->filter('h3')->text();
        $price= $node->filter('.text-lg')->text();
        $img = $node->filter('img')->attr('src');
        $color = $node->filter('div.my-4 div.flex div.px-2 span')->attr('data-colour');
        $capacity = $node->filter('span.product-capacity')->text();
        $availibity = $node->filter('div.text-sm')->text();
        $isAvailable = str_contains($availibity,'In') ? true : false;
        $shippingText = $node->filter('div.bg-white > div')->last()->text();
        $shippingDate = $node->filter('div.bg-white > div')->last()->text();
        $productArray = array(

          'title' => $title,
          'price' => $this->removeSybol($price),
          'imageUrl'=> $this->imgUrl('https://www.magpiehq.com/developer-challenge/smartphones'.$img),
          'capacityMB' => $this->convertToMb($capacity),
          'colour' => $color,
          'availabilityText' => $availibity,
          'isAvailable' => $isAvailable,
          'shippingText' =>$shippingText,
          'shippingDate' =>$shippingDate
        );
        
        
        $products[] = array_unique($productArray);
        

      }
      
      $json = json_encode($products);
      file_put_contents("output.json", $json);
    }
   

     
}

$product = new Product();
$product->run();
