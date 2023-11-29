<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Searchurl;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class SaveProductList extends Command {

    protected $signature = 'app:save-product-list {--url= : url of products}';

    protected $description = 'Command allowing given a url to store the first 5 products';

    public function handle() {
        try{
            //Obtenemos la url del usuario y verificamos que la variable url contenga los datos, si no hacemos saltar un error
            $url = $this->option('url');

            if (!$url) {
                $this->error('You must provide a --url option');
                return;
            }

            $this->info("Uploading the products from: $url");

            // Creamos un cliente con el paquete "Guzzle" y desactivamos temporalmente la verificacion ssl. 
            $client = new Client(['verify' => false]);

            // Realizamos la solicitud http
            $response = $client->request('GET', $url);

            // Comprobar el status code si no da 200 haremos saltar un error
            $statusCode = $response->getStatusCode();
            if (!$statusCode and $statusCode != 200) {
                $this->error("An error has occurred in the request");
                return;
            }

            //Si todo es correcto llamamos a la funcion getContent() pasandole por parametro la response
            $productData = $this->getContent($response);

            if (!empty($productData)) {
                $this->storeData($url, $productData);
                $this->info("Products successfully saved to the database");
            } else {
                $this->error("There are no products to store in this link");
            }
            
        } catch (\Exception $e) {
            error_log('Error en getContent (bucle each): ' . $e->getMessage());
        }
    }

    protected function getContent($response) {
        try {
            //Obtenemos el content de la url
            $content = $response->getBody()->getContents();
            
            // Utilizar Symfony DomCrawler para manejar mas comodamente el HTML
            $crawler = new Crawler($content);

            // Filtramos todos los elementos por ".product-card__parent"
            $productElements = $crawler->filter('.product-card__parent');

            $result = [];
            
            $productElements->each(function (Crawler $elements) use (&$result) {
                try {
                    // Obtenemos el precio de 'product-card__parent'
                    $price = $elements->attr('app_price');
                    
                    //Accedemos al elemento "product-card__media" y de alli obtenemos el link de la imagen
                    $productMedia = $elements->filter('.product-card__media');
                    $image_url = $productMedia->filter('img')->attr('src');

                    // Acceder al elemento 'product-card__detail' dentro de 'product-card__parent' y obtenemos el titulo y link
                    $productDetails = $elements->filter('.product-card__detail');
                    $title = $productDetails->filter('.product-card__title')->text();
                    $link = $productDetails->filter('a')->attr('href');
                    
                    $result[] = array(
                        'title' => $title,
                        'price' => $price,
                        'image_url' => $image_url,
                        'data_link' => $link
                    );

                    //si ya tenemos los 5 productos salimos del bucle
                    if(count($result) == 5){
                       //
                    }

                } catch (\Exception $e) {
                    error_log('Error en getContent (bucle each): ' . $e->getMessage());
                }
            });

            return $result;

        } catch (\Exception $e) {
            error_log('Error en getContent: ' . $e->getMessage());
            return [];
        }
    }
    
    protected function storeData($URL,$productsData){   
        try{

            //Primero registramos la busqueda que ha hecho el usuario.
            $searchurlModel = new Searchurl();
            $searchId = Str::uuid()->toString();

            $searchurlModel->id = $searchId;
            $searchurlModel->searchURL = $URL;

            $searchurlModel->save();

            foreach ($productsData as $productData) {
                // buscamos si ya existe este producto en nuestra base de datos.
                $existingProduct = Product::where('data_link', $productData['data_link'])->first();

                // Si no existe, crear y guardar el nuevo producto
                if (!$existingProduct) {
                    $productModel = new Product();

                    $productModel->title = $productData['title'];
                    $productModel->price = $productData['price'];
                    $productModel->image_url = $productData['image_url'];
                    $productModel->data_link = $productData['data_link'];
                    $productModel->searchurl_id = $searchId;
                    //Para este ejemplo de codigo, podremos a mano el id 1
                    $productModel->company_id = "1";
                    $productModel->category_id = "1";

                    $productModel->save();
                } else {
                    $this->info("Product " . $productData['title'] . " already exists in the database");
                }
            }
        } catch (\Exception $e) {
            error_log('Error' . $e->getMessage());
        }
    }
}
