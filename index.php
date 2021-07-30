<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="#" type="image/x-icon">
    <title>Crawler</title>
    <style>
        body{
            background-color: black;
            color: white;
        }
    </style>
</head>
<body>
    <?php
        set_time_limit(120);
        require_once('dom.php');
        $start = "https://shopify.bayparkphotos.com.au/collections/all?page=";

        function crawl_page($url)
        {

            $count = 1;
            $fp = fopen('file.csv', 'a');
            for($i = 1; $i<=113; $i++)
            {
                $link = $url.strval($i); 
            
                $options  = array('http' => array('user_agent' => 'Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0'));
                $context  = stream_context_create($options);
                
                $html = file_get_html($link,false,$context);
                    
                $links = $html->find('a.product-grid-item');

                foreach($links as $l)
                {
                    $product_url = "https://shopify.bayparkphotos.com.au".$l->href;
                    
                    $page = file_get_html($product_url, false, $context);
                    $title = "";
                    $description = "";
                    $price = "";
                    $img = "";
                    if(!empty($page))
                    {
                        if($page->find('title',0) != NULL)
                        {
                            $title=$page->find('title',0)->plaintext;
                        }
                        
                        if($page->find('meta[name=description]',0) != NULL)
                        {
                            $d = $page->find('meta[name=description]',0);
                            $description = $d->content;
                        }

                        if($page->find('meta[itemprop=price]',0) != NULL)
                        {
                            $p = $page->find('meta[itemprop=price]',0);
                            $price = $p->content;
                        }
                        
                        if($page->find('img.lazyload',0) != NULL)
                        {
                            $m = $page->find('img.lazyload',0);
                            $img = $m->src;
                        }

                        $array = array($title, $description, $price, $img, $product_url);

                        fputcsv($fp, $array);
                    }
                    $page = "";
                }
                $html = "";
            }
            fclose($fp);
        }
        
        crawl_page($start);

        // var_dump($pages);
        

    ?>
</body>
</html>